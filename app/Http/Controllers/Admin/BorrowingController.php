<?php

/**
 * Borrowing Controller (Admin)
 * 
 * Controller ini bertanggung jawab untuk mengelola semua operasi terkait peminjaman laptop
 * di sisi administrator, termasuk menampilkan, mengupdate, menyetujui, dan menolak peminjaman.
 * 
 * @package App\Http\Controllers\Admin
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Laptop;
use App\Models\TransaksiDenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BorrowingController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman dengan filter pencarian dan status.
     */
    public function index(Request $request): View
    {
        // Inisialisasi query dengan eager loading relasi user dan laptop
        $query = Borrowing::with(['user', 'laptop']);

        // Filter berdasarkan pencarian
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('laptop', function($laptopQuery) use ($search) {
                    $laptopQuery->where('merk', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan status peminjaman
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_selesai') && $request->tanggal_selesai) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_selesai);
        }

        // Filter berdasarkan nama/email peminjam
        if ($request->has('peminjam') && $request->peminjam) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->peminjam . '%')
                  ->orWhere('email', 'like', '%' . $request->peminjam . '%');
            });
        }

        // Ambil data dengan urutan terbaru dan pagination
        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik untuk dashboard admin
        $statistics = [
            'total' => Borrowing::count(),
            'pending' => Borrowing::where('status', 'pending')->count(),
            'active' => Borrowing::where('status', 'aktif')->count(),
            'returned' => Borrowing::where('status', 'selesai')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'rejected' => Borrowing::where('status', 'ditolak')->count(),
            'cancelled' => Borrowing::where('status', 'batal')->count(),
        ];

        return view('admin.borrowings.index', compact('borrowings', 'statistics'));
    }

    /**
     * Menampilkan detail peminjaman berdasarkan ID.
     */
    public function show($id): View
    {
        $borrowing = Borrowing::with(['user', 'laptop', 'approver'])
            ->findOrFail($id);

        return view('admin.borrowings.show', compact('borrowing'));
    }

    /**
     * Menampilkan form edit peminjaman.
     */
    public function edit($id): View
    {
        $borrowing = Borrowing::findOrFail($id);
        $users = User::where('role', 'user')->orderBy('name')->get();
        $laptops = Laptop::orderBy('merk')->orderBy('model')->get();

        return view('admin.borrowings.form', compact('borrowing', 'users', 'laptops'));
    }

    /**
     * Mengupdate data peminjaman yang sudah ada.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $borrowing = Borrowing::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'laptop_id' => 'required|exists:laptops,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'tujuan' => 'required|in:meeting,presentasi,training,work_from_home,proyek,lainnya',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Jika laptop yang dipinjam berubah
            if ($borrowing->laptop_id != $validated['laptop_id']) {
                // Kembalikan status laptop lama menjadi tersedia
                $oldLaptop = Laptop::find($borrowing->laptop_id);
                if ($oldLaptop && $oldLaptop->status == 'dipinjam') {
                    $oldLaptop->update(['status' => 'tersedia']);
                }

                // Update status laptop baru menjadi dipinjam
                $newLaptop = Laptop::find($validated['laptop_id']);
                if ($newLaptop && $newLaptop->status == 'tersedia') {
                    $newLaptop->update(['status' => 'dipinjam']);
                } else {
                    throw new \Exception('Laptop baru tidak tersedia.');
                }
            }

            $borrowing->update($validated);

            DB::commit();

            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menyetujui peminjaman yang masih pending.
     */
    public function approve($id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $borrowing = Borrowing::with('laptop')->findOrFail($id);

            if ($borrowing->status !== 'pending') {
                throw new \Exception('Status peminjaman tidak valid untuk disetujui.');
            }

            if ($borrowing->laptop && $borrowing->laptop->status != 'tersedia') {
                throw new \Exception('Laptop tidak tersedia untuk dipinjam.');
            }

            if ($borrowing->laptop) {
                $borrowing->laptop->update(['status' => 'dipinjam']);
            }

            $borrowing->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'waktu_approve' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Peminjaman disetujui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menolak peminjaman yang masih pending.
     */
    public function reject(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'alasan_ditolak' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $borrowing = Borrowing::findOrFail($id);

            if ($borrowing->status !== 'pending') {
                throw new \Exception('Status peminjaman tidak valid untuk ditolak.');
            }

            $borrowing->update([
                'status' => 'ditolak',
                'alasan_ditolak' => $request->alasan_ditolak,
                'approved_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Peminjaman ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data peminjaman.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $borrowing = Borrowing::with('laptop')->findOrFail($id);
            
            $transaksiDenda = TransaksiDenda::where('peminjaman_id', $id)->first();
            if ($transaksiDenda && $transaksiDenda->status_pembayaran != 'lunas') {
                throw new \Exception('Tidak dapat menghapus peminjaman yang memiliki denda belum lunas.');
            }
            
            if (in_array($borrowing->status, ['approved', 'aktif']) && !$borrowing->tanggal_kembali) {
                if ($borrowing->laptop) {
                    $borrowing->laptop->update(['status' => 'tersedia']);
                }
            }
            
            if ($transaksiDenda) {
                $transaksiDenda->delete();
            }
            
            $borrowing->delete();
            
            DB::commit();
            
            return redirect()->route('admin.borrowings.index')
                ->with('success', 'Data peminjaman berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Update status peminjaman (untuk approve/reject via modal)
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:approved,ditolak,selesai,batal',
            'catatan' => 'nullable|string|max:500',
            'alasan_ditolak' => 'required_if:status,ditolak|nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            
            $borrowing = Borrowing::with('laptop')->findOrFail($id);
            $newStatus = $request->status;
            
            if ($newStatus == 'approved') {
                if ($borrowing->status !== 'pending') {
                    throw new \Exception('Status peminjaman tidak valid untuk disetujui.');
                }
                
                if ($borrowing->laptop && $borrowing->laptop->status != 'tersedia') {
                    throw new \Exception('Laptop tidak tersedia untuk dipinjam.');
                }
                
                if ($borrowing->laptop) {
                    $borrowing->laptop->update(['status' => 'dipinjam']);
                }
                
                $borrowing->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'waktu_approve' => now(),
                ]);
                
                $message = 'Peminjaman berhasil disetujui.';
                
            } elseif ($newStatus == 'ditolak') {
                if ($borrowing->status !== 'pending') {
                    throw new \Exception('Status peminjaman tidak valid untuk ditolak.');
                }
                
                $borrowing->update([
                    'status' => 'ditolak',
                    'alasan_ditolak' => $request->alasan_ditolak,
                    'approved_by' => auth()->id(),
                ]);
                
                $message = 'Peminjaman ditolak.';
                
            } else {
                throw new \Exception('Status tidak valid.');
            }
            
            DB::commit();
            
            return redirect()->route('admin.borrowings.index')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Get borrowing code for AJAX (untuk modal approve/reject)
     */
    public function getKodePeminjaman($id): JsonResponse
    {
        $borrowing = Borrowing::findOrFail($id);
        $kode = $borrowing->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($borrowing->created_at)) . '-' . str_pad($borrowing->id, 4, '0', STR_PAD_LEFT);
        
        return response()->json(['kode' => $kode]);
    }

/**
 * Get borrowing detail for AJAX (untuk modal show)
 */
public function getDetail($id): JsonResponse
{
    try {
        $borrowing = Borrowing::with(['user', 'laptop'])->findOrFail($id);
        
        $data = [
            'success' => true,
            'data' => [
                'id' => $borrowing->id,
                'kode_peminjaman' => $borrowing->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($borrowing->created_at)) . '-' . str_pad($borrowing->id, 4, '0', STR_PAD_LEFT),
                'status' => $borrowing->status,
                'tanggal_pinjam' => $borrowing->tanggal_pinjam,
                'tanggal_kembali_rencana' => $borrowing->tanggal_kembali_rencana,
                'tanggal_kembali' => $borrowing->tanggal_kembali,
                'tujuan' => $borrowing->tujuan,
                'keterangan' => $borrowing->keterangan,
                'denda' => $borrowing->denda,
                'user' => $borrowing->user ? [
                    'name' => $borrowing->user->name,
                    'email' => $borrowing->user->email,
                    'phone' => $borrowing->user->phone ?? '-',
                    'nim' => $borrowing->user->nim ?? '-',
                ] : null,
                'laptop' => $borrowing->laptop ? [
                    'merk' => $borrowing->laptop->merk,
                    'model' => $borrowing->laptop->model,
                    'serial_number' => $borrowing->laptop->serial_number,
                    'kode_alat' => $borrowing->laptop->kode_alat ?? '-',
                    'kondisi' => $borrowing->laptop->kondisi ?? '-',
                ] : null,
            ]
        ];
        
        return response()->json($data);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan: ' . $e->getMessage()
        ], 404);
    }
}

    /**
     * Process return of laptop
     */
    public function return(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'tanggal_kembali' => 'required|date',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            
            $borrowing = Borrowing::with('laptop')->findOrFail($id);
            
            if (!in_array($borrowing->status, ['approved', 'aktif'])) {
                throw new \Exception('Peminjaman tidak sedang aktif.');
            }
            
            if ($borrowing->tanggal_kembali) {
                throw new \Exception('Laptop sudah dikembalikan.');
            }
            
            // Hitung denda jika terlambat
            $denda = 0;
            $tanggalRencana = \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana);
            $tanggalKembali = \Carbon\Carbon::parse($request->tanggal_kembali);
            
            if ($tanggalKembali->gt($tanggalRencana)) {
                $hariTerlambat = $tanggalKembali->diffInDays($tanggalRencana);
                $hargaSewa = $borrowing->laptop->harga_sewa_harian ?? 50000;
                $denda = $hariTerlambat * $hargaSewa;
            }
            
            // Update peminjaman
            $borrowing->update([
                'status' => 'selesai',
                'tanggal_kembali' => $request->tanggal_kembali,
                'catatan_pengembalian' => $request->catatan,
                'denda' => $denda,
            ]);
            
            // Update laptop
            if ($borrowing->laptop) {
                $borrowing->laptop->update([
                    'status' => 'tersedia',
                    'kondisi' => $request->kondisi,
                ]);
            }
            
            DB::commit();
            
            $message = 'Laptop berhasil dikembalikan.';
            if ($denda > 0) {
                $message .= ' Denda: Rp ' . number_format($denda, 0, ',', '.');
            }
            
            return redirect()->route('admin.borrowings.index')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}