@extends('layouts.app')

@section('title', 'Peminjaman Saya')
@section('header-icon', 'fas fa-hand-holding')
@section('header-title', 'Peminjaman Saya')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12">


            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter Status -->
<div class="d-flex justify-content-between align-items-center gap-3 mb-4">
    <!-- Filter Section - Kiri -->
    <div class="d-flex gap-2 flex-grow-1">
        <form method="GET" action="{{ route('user.peminjaman.index') }}" class="d-flex gap-2 w-100">
            <select class="form-select" name="status" style="max-width: 200px;">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="pending_return" {{ request('status') == 'pending_return' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
        </form>
    </div>
    
    <!-- Button Section - Kanan -->
    <div>
        <a href="{{ route('user.alat') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Pinjam Alat Baru
        </a>
    </div>
</div>

            <!-- Daftar Peminjaman -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Daftar Peminjaman</h5>
                </div>
                <div class="card-body">
                    @if($peminjaman->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Alat</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Rencana Kembali</th>
                                        <th>Tgl Kembali</th>
                                        <th>Status</th>
                                        <th>Denda</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peminjaman as $index => $item)
                                    <tr>
                                        <td>{{ $peminjaman->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                #{{ $item->kode_peminjaman ?? 'PJ-'.$item->id }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $item->laptop->merk ?? $item->nama_alat ?? '-' }} {{ $item->laptop->model ?? '' }}</div>
                                            <small class="text-muted">{{ $item->laptop->serial_number ?? $item->kode_alat ?? '-' }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($item->tanggal_kembali)
                                                {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'pending' => ['badge' => 'warning', 'text' => 'Menunggu'],
                                                    'approved' => ['badge' => 'success', 'text' => 'Disetujui'],
                                                    'aktif' => ['badge' => 'primary', 'text' => 'Aktif'],
                                                    'pending_return' => ['badge' => 'info', 'text' => 'Menunggu Konfirmasi'],
                                                    'selesai' => ['badge' => 'secondary', 'text' => 'Selesai'],
                                                    'ditolak' => ['badge' => 'danger', 'text' => 'Ditolak'],
                                                    'aktif_denda' => ['badge' => 'warning', 'text' => 'Aktif (Denda)'],
                                                ];
                                                $status = $statusMap[$item->status] ?? ['badge' => 'secondary', 'text' => ucfirst($item->status)];
                                            @endphp
                                            <span class="badge bg-{{ $status['badge'] }}">
                                                {{ $status['text'] }}
                                            </span>
                                            
                                            @if($item->status == 'pending_return')
                                                <br>
                                                <small class="text-muted">Menunggu konfirmasi petugas</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->denda > 0)
                                                <span class="badge bg-warning">
                                                    Rp {{ number_format($item->denda, 0, ',', '.') }}
                                                </span>
                                                @if($item->is_denda_dibayar == 0)
                                                    <br>
                                                    <small class="text-danger">Belum dibayar</small>
                                                @endif
                                            @else
                                                <span class="badge bg-success">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Tombol Hapus hanya untuk status selesai -->
                                            @if($item->status == 'selesai')
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm rounded-circle" 
                                                        title="Hapus Riwayat"
                                                        onclick="confirmDelete({{ $item->id }}, '{{ $item->kode_peminjaman ?? 'PJ-'.$item->id }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $item->id }}" 
                                                      action="{{ route('user.peminjaman.destroy', $item->id) }}" 
                                                      method="POST" 
                                                      style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($peminjaman->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $peminjaman->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-inbox fa-4x text-muted"></i>
                            </div>
                            <h5>Belum Ada Peminjaman</h5>
                            <p class="text-muted mb-3">Anda belum melakukan peminjaman alat.</p>
                            <a href="{{ route('user.alat') }}" class="btn btn-primary">
                                <i class="fas fa-laptop me-2"></i>Pinjam Alat Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Keterangan Status -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Keterangan Status:</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <span class="badge bg-warning">Menunggu</span> : Menunggu persetujuan
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-success">Disetujui</span> : Disetujui, siap diambil
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-primary">Aktif</span> : Sedang dipinjam
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-info">Menunggu Konfirmasi</span> : Pengembalian diajukan
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-secondary">Selesai</span> : Selesai
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-danger">Ditolak</span> : Ditolak
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-warning">Aktif (Denda)</span> : Ada denda belum bayar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Confirm delete function
    function confirmDelete(id, kode) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Riwayat peminjaman <strong>${kode}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #eef2f7;
}
.badge {
    font-weight: 500;
    padding: 5px 10px;
}
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
.btn-outline-danger {
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50% !important;
    transition: all 0.2s;
}
.btn-outline-danger:hover {
    transform: scale(1.1);
}
</style>
@endsection