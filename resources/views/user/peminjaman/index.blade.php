{{-- resources/views/user/peminjaman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Saya')
@section('header-icon', 'fas fa-hand-holding')
@section('header-title', 'Riwayat Peminjaman Saya')

@section('content')
<div class="container-fluid px-0">
    <!-- STATISTIK PEMINJAMAN USER -->
    <div class="row mb-4 g-3">
        <div class="col-md-3 col-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['pending'] ?? 0) }}</div>
                <div class="stats-label">Menunggu</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['approved'] ?? 0) }}</div>
                <div class="stats-label">Disetujui</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['completed'] ?? 0) }}</div>
                <div class="stats-label">Selesai</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['rejected'] ?? 0) }}</div>
                <div class="stats-label">Ditolak</div>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Daftar Peminjaman -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Daftar Peminjaman Saya
                </h5>
                <a href="{{ route('user.peminjaman.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Pinjam Baru
                </a>
            </div>

            <!-- Filter -->
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <form method="GET" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Sedang Dipinjam</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari</label>
                            <input type="text" class="form-control" name="search" placeholder="Kode atau nama laptop" 
                                   value="{{ request('search') }}" onkeyup="if(event.keyCode==13) this.form.submit()">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <a href="{{ route('user.peminjaman.index') }}" class="btn btn-secondary">
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
                            <th>Kode Peminjaman</th>
                            <th>Laptop</th>
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman as $item)
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'approved' => 'info',
                                'aktif' => 'primary',
                                'selesai' => 'success',
                                'ditolak' => 'danger',
                                'batal' => 'secondary',
                            ];
                            
                            $statusLabels = [
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'aktif' => 'Dipinjam',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                                'batal' => 'Dibatalkan',
                            ];
                            
                            $kodePeminjaman = $item->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($item->created_at)) . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $kodePeminjaman }}</span>
                                <br><small class="text-muted">#{{ $item->id }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item->laptop->merk ?? '-' }} {{ $item->laptop->model ?? '' }}</div>
                                <small class="text-muted">SN: {{ $item->laptop->serial_number ?? '-' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->translatedFormat('d M Y') }}
                                @if($item->status == 'approved' && \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast())
                                    <span class="badge bg-danger ms-1">Terlambat!</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
                                </span>
                                @if($item->status == 'ditolak' && $item->alasan_ditolak)
                                    <br><small class="text-danger">Alasan: {{ $item->alasan_ditolak }}</small>
                                @endif
                                @if($item->denda > 0 && $item->status == 'selesai')
                                    <br><small class="text-warning">Denda: Rp {{ number_format($item->denda, 0, ',', '.') }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('user.peminjaman.show', $item->id) }}" 
                                       class="btn btn-outline-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Tombol Konfirmasi Pengambilan (jika status approved) --}}
                                    @if($item->status == 'approved')
                                        <button type="button" 
                                                class="btn btn-outline-success" 
                                                title="Konfirmasi Pengambilan"
                                                onclick="confirmTake({{ $item->id }})">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    
                                    {{-- Tombol Batalkan (jika status pending) --}}
                                    @if($item->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Batalkan Peminjaman"
                                                onclick="cancelBorrowing({{ $item->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    {{-- Tombol Hapus (jika status batal atau ditolak) --}}
                                    @if(in_array($item->status, ['batal', 'ditolak']))
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Hapus Riwayat"
                                                onclick="deleteHistory({{ $item->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-hand-holding fa-3x mb-3 d-block"></i>
                                    <h5 class="mb-1">Belum ada peminjaman</h5>
                                    <p class="mb-0">Silakan ajukan peminjaman laptop</p>
                                    <a href="{{ route('user.peminjaman.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-1"></i> Ajukan Peminjaman
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $peminjaman->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Penolakan -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i> Alasan Penolakan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="rejectReasonText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfirmasi Pengambilan Laptop
window.confirmTake = function(id) {
    Swal.fire({
        title: 'Konfirmasi Pengambilan',
        text: 'Apakah Anda sudah menerima laptop yang dipinjam?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Sudah Saya Terima',
        cancelButtonText: 'Belum'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/user/peminjaman/${id}/take`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'POST'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
};

// Batalkan Peminjaman (oleh user)
window.cancelBorrowing = function(id) {
    Swal.fire({
        title: 'Batalkan Peminjaman?',
        text: 'Apakah Anda yakin ingin membatalkan peminjaman ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Kembali'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/user/peminjaman/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
};

// Hapus Riwayat
window.deleteHistory = function(id) {
    Swal.fire({
        title: 'Hapus Riwayat?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/user/peminjaman/${id}/force`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
};

// Auto dismiss alert
setTimeout(() => {
    $('.alert-dismissible').alert('close');
}, 5000);
</script>
@endsection