{{-- resources/views/petugas/transaksi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Transaksi Denda')
@section('header-icon', 'fas fa-receipt')
@section('header-title', 'Manajemen Transaksi Denda')

@section('content')
<div class="container-fluid px-0">


{{-- Ganti bagian statistik dengan yang lebih akurat --}}
<div class="row mb-4 g-3">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stats-number">{{ number_format($total_transaksi ?? 0) }}</div>
            <div class="stats-label">Total Transaksi</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-money-bill"></i>
            </div>
            <div class="stats-number">Rp {{ number_format(max(0, $total_nominal_denda ?? 0), 0, ',', '.') }}</div>
            <div class="stats-label">Total Denda</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="stats-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">Rp {{ number_format(max(0, $total_dibayar ?? 0), 0, ',', '.') }}</div>
            <div class="stats-label">Sudah Dibayar</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stats-number">Rp {{ number_format(max(0, $total_belum_bayar ?? 0), 0, ',', '.') }}</div>
            <div class="stats-label">Belum Dibayar</div>
        </div>
    </div>
</div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Daftar Transaksi -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="fas fa-list text-primary me-2"></i>
                Daftar Transaksi Denda
            </h5>

            <!-- Filter -->
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select class="form-select" name="status_pembayaran" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="belum_lunas" {{ request('status_pembayaran') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="sebagian_lunas" {{ request('status_pembayaran') == 'sebagian_lunas' ? 'selected' : '' }}>Sebagian Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Kode Peminjaman</th>
                <th>Peminjam</th>
                <th>Laptop</th>
                <th>Total Denda</th>
                <th>Dibayar</th>
                <th>Sisa</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis ?? [] as $item)
            @php
                // ✅ Gunakan nilai absolut untuk menghindari negatif
                $totalDenda = abs($item->total_denda ?? 0);
                $dendaDibayar = abs($item->denda_dibayar ?? 0);
                $sisa = max(0, $totalDenda - $dendaDibayar);
                
                // Ambil data peminjaman dengan aman
                $peminjaman = $item->peminjaman;
                $kodePeminjaman = $peminjaman ? ($peminjaman->kode_peminjaman ?? 'PINJ-' . $peminjaman->id) : 'Transaksi #' . $item->id;
                $laptopName = $peminjaman && $peminjaman->laptop ? ($peminjaman->laptop->merk . ' ' . $peminjaman->laptop->model) : '-';
                $userName = $item->user->name ?? ($peminjaman->user->name ?? '-');
                
                $statusColors = [
                    'lunas' => 'success',
                    'belum_lunas' => 'danger',
                    'sebagian_lunas' => 'warning',
                    'tidak_ada_denda' => 'secondary'
                ];
                $statusLabel = ucfirst(str_replace('_', ' ', $item->status_pembayaran ?? 'unknown'));
            @endphp
            <tr>
                <td>#{{ $item->id }}</td>
                <td>
                    <strong>{{ $kodePeminjaman }}</strong>
                </td>
                <td>{{ $userName }}</td>
                <td>{{ $laptopName }}</td>
                <td class="text-danger fw-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                <td class="text-success">Rp {{ number_format($dendaDibayar, 0, ',', '.') }}</td>
                <td class="text-warning fw-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                <td>
                    <span class="badge bg-{{ $statusColors[$item->status_pembayaran] ?? 'secondary' }}">
                        {{ $statusLabel }}
                    </span>
                </td>
                <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('petugas.transaksi.show', $item->id) }}" 
                           class="btn btn-outline-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
@if(($item->status_pembayaran ?? '') != 'lunas' && $sisa > 0)
    <a href="{{ route('petugas.transaksi.bayar.form', $item->id) }}" 
       class="btn btn-sm btn-success" 
       title="Bayar Denda">
        <i class="fas fa-money-bill"></i> Bayar
    </a>
@endif
                        <a href="{{ route('petugas.transaksi.cetak', $item->id) }}" 
                           class="btn btn-outline-secondary" 
                           title="Cetak Struk"
                           target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 d-block"></i>
                            <h5 class="mb-1">Belum ada transaksi denda</h5>
                            <p class="mb-0">Transaksi akan muncul setelah ada pengembalian dengan denda</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ ($transaksis ?? [])->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- resources/views/petugas/transaksi/index.blade.php --}}
{{-- Tambahkan modal ini di bagian bawah file, sebelum @endsection --}}

<!-- Modal Bayar Denda -->
<div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="bayarModalLabel">
                    <i class="fas fa-money-bill me-2"></i> Pembayaran Denda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bayarForm">
                @csrf
                <input type="hidden" id="transaksi_id" name="transaksi_id">
                <div class="modal-body">
                    <div class="alert alert-info mb-3" id="infoDenda">
                        <strong>Informasi Denda:</strong><br>
                        Menunggu data...
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" 
                               class="form-control" min="1000" step="1000" required>
                        <small class="text-muted">Minimal Rp 1.000</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="tunai">💰 Tunai</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                            <option value="qris">📱 QRIS</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Pastikan DOM sudah siap
$(document).ready(function() {
    console.log('Document ready - Transaksi Page');
    console.log('Base URL:', window.location.origin);
    
    // Setup CSRF Token untuk semua AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// ===== FUNGSI BAYAR DENDA =====
window.bayarDenda = function(id) {
    console.log('=== BAYAR DENDA DIPANGGIL ===');
    console.log('ID Transaksi:', id);
    
    if (!id) {
        Swal.fire('Error!', 'ID Transaksi tidak valid', 'error');
        return;
    }
    
    // Tampilkan loading
    Swal.fire({
        title: 'Mengambil Data...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Gunakan URL lengkap
    var url = window.location.origin + '/petugas/transaksi/' + id + '/data';
    console.log('Fetching data from:', url);
    
    // Ambil data transaksi via AJAX
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        timeout: 30000, // 30 seconds timeout
        success: function(response) {
            console.log('Response getData:', response);
            Swal.close();
            
            if (response.success && response.data) {
                // Set nilai ke modal
                $('#transaksi_id').val(id);
                $('#infoDenda').html(`
                    <strong>💸 Detail Denda:</strong><br>
                    <hr class="my-1">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="50%">Total Denda:</td>
                            <td class="text-end"><strong>Rp ${response.data.total_denda.toLocaleString('id-ID')}</strong></td>
                        </tr>
                        <tr>
                            <td>Sudah Dibayar:</td>
                            <td class="text-end">Rp ${response.data.dibayar.toLocaleString('id-ID')}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Sisa Tagihan:</strong></td>
                            <td class="text-end text-danger"><strong>Rp ${response.data.sisa.toLocaleString('id-ID')}</strong></td>
                        </tr>
                    </table>
                `);
                
                // Set max dan default value
                $('#jumlah_bayar').attr('max', response.data.sisa);
                $('#jumlah_bayar').val(response.data.sisa);
                $('#jumlah_bayar').attr('placeholder', 'Maksimal Rp ' + response.data.sisa.toLocaleString('id-ID'));
                
                // Reset metode pembayaran
                $('#metode_pembayaran').val('');
                
                // Tampilkan modal
                $('#bayarModal').modal('show');
                
            } else {
                Swal.fire('Error!', response.message || 'Gagal mengambil data transaksi', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('AJAX Error Detail:');
            console.error('URL:', url);
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('ReadyState:', xhr.readyState);
            console.error('Status Code:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            
            let errorMsg = 'Gagal mengambil data transaksi';
            
            if (status === 'timeout') {
                errorMsg = 'Koneksi timeout. Silakan coba lagi.';
            } else if (status === 'error' && xhr.status === 0) {
                errorMsg = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMsg = 'Transaksi tidak ditemukan';
            } else if (xhr.status === 500) {
                errorMsg = 'Terjadi kesalahan pada server';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `
                    <div class="text-start">
                        <p><strong>Error:</strong> ${errorMsg}</p>
                        <hr>
                        <small class="text-muted">Status: ${status}</small><br>
                        <small class="text-muted">Silakan refresh halaman dan coba lagi.</small>
                    </div>
                `,
                confirmButtonText: 'OK'
            });
        }
    });
};

// ===== SUBMIT FORM PEMBAYARAN =====
$(document).ready(function() {
    $('#bayarForm').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#transaksi_id').val();
        let jumlah = $('#jumlah_bayar').val();
        let metode = $('#metode_pembayaran').val();
        
        console.log('=== SUBMIT PEMBAYARAN ===');
        console.log('ID:', id);
        console.log('Jumlah:', jumlah);
        console.log('Metode:', metode);
        
        // Validasi
        if (!id) {
            Swal.fire('Error!', 'ID Transaksi tidak valid', 'error');
            return;
        }
        
        if (!jumlah || jumlah < 1000) {
            Swal.fire('Error!', 'Jumlah bayar minimal Rp 1.000', 'error');
            return;
        }
        
        if (!metode) {
            Swal.fire('Error!', 'Silakan pilih metode pembayaran', 'error');
            return;
        }
        
        // Konfirmasi
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `
                <div class="text-start">
                    <p>Detail pembayaran:</p>
                    <table class="table table-sm">
                        <tr>
                            <td width="40%">ID Transaksi</td>
                            <td>: <strong>#${id}</strong></td>
                        </tr>
                        <tr>
                            <td>Jumlah Bayar</td>
                            <td>: <strong>Rp ${parseInt(jumlah).toLocaleString('id-ID')}</strong></td>
                        </tr>
                        <tr>
                            <td>Metode</td>
                            <td>: <strong>${metode.toUpperCase()}</strong></td>
                        </tr>
                    </table>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bayar!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses Pembayaran...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                var bayarUrl = window.location.origin + '/petugas/transaksi/' + id + '/bayar';
                console.log('Sending payment to:', bayarUrl);
                
                // Kirim request
                $.ajax({
                    url: bayarUrl,
                    type: 'POST',
                    data: {
                        jumlah_bayar: jumlah,
                        metode_pembayaran: metode,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {
                        console.log('Success response:', response);
                        Swal.close();
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                html: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#bayarModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error Detail:');
                        console.error('URL:', bayarUrl);
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Status Code:', xhr.status);
                        console.error('Response Text:', xhr.responseText);
                        
                        Swal.close();
                        
                        let errorMsg = 'Terjadi kesalahan saat memproses pembayaran';
                        
                        if (status === 'timeout') {
                            errorMsg = 'Koneksi timeout. Silakan coba lagi.';
                        } else if (status === 'error' && xhr.status === 0) {
                            errorMsg = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Gagal!',
                            html: `
                                <div class="text-start">
                                    <p><strong>Error:</strong> ${errorMsg}</p>
                                    <hr>
                                    <small class="text-muted">Status Code: ${xhr.status}</small><br>
                                    <small class="text-muted">Silakan cek koneksi internet dan coba lagi.</small>
                                </div>
                            `,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
});

console.log('Fungsi bayarDenda tersedia:', typeof bayarDenda === 'function');
</script>
@endpush