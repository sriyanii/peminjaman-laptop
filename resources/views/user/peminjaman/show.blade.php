{{-- resources/views/user/peminjaman/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('header-icon', 'fas fa-info-circle')
@section('header-title', 'Detail Peminjaman')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'info',
                            'aktif' => 'primary',
                            'selesai' => 'success',
                            'ditolak' => 'danger',
                            'batal' => 'secondary',
                        ];
                        $statusIcons = [
                            'pending' => 'fa-clock',
                            'approved' => 'fa-check-circle',
                            'aktif' => 'fa-laptop',
                            'selesai' => 'fa-check-double',
                            'ditolak' => 'fa-times-circle',
                            'batal' => 'fa-ban',
                        ];
                    @endphp
                    <div class="mb-3">
                        <i class="fas {{ $statusIcons[$peminjaman->status] ?? 'fa-question-circle' }} fa-3x text-{{ $statusColors[$peminjaman->status] ?? 'secondary' }}"></i>
                    </div>
                    <h4 class="mb-2">
                        Status: 
                        <span class="badge bg-{{ $statusColors[$peminjaman->status] ?? 'secondary' }} fs-6">
                            {{ ucfirst($peminjaman->status) }}
                        </span>
                    </h4>
                    @if($peminjaman->status == 'ditolak' && $peminjaman->alasan_ditolak)
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Alasan Ditolak:</strong> {{ $peminjaman->alasan_ditolak }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Peminjaman -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Informasi Peminjaman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Kode Peminjaman</label>
                            <p class="fw-bold mb-0">
                                {{ $peminjaman->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($peminjaman->created_at)) . '-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">ID Peminjaman</label>
                            <p class="fw-bold mb-0">#{{ $peminjaman->id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Pengajuan</label>
                            <p class="mb-0">{{ $peminjaman->created_at->translatedFormat('d F Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Disetujui</label>
                            <p class="mb-0">
                                @if($peminjaman->waktu_approve)
                                    {{ \Carbon\Carbon::parse($peminjaman->waktu_approve)->translatedFormat('d F Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Laptop</label>
                            <p class="fw-bold mb-0">
                                {{ $peminjaman->laptop->merk ?? '-' }} {{ $peminjaman->laptop->model ?? '' }}
                            </p>
                            <small class="text-muted">SN: {{ $peminjaman->laptop->serial_number ?? '-' }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tujuan Peminjaman</label>
                            <p class="mb-0">{{ ucfirst($peminjaman->tujuan) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Pinjam</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Rencana Kembali</label>
                            <p class="mb-0 {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->isPast() && $peminjaman->status != 'selesai' ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->translatedFormat('d F Y') }}
                                @if(\Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->isPast() && $peminjaman->status != 'selesai')
                                    <i class="fas fa-exclamation-triangle ms-1"></i> (Terlambat)
                                @endif
                            </p>
                        </div>
                        @if($peminjaman->tanggal_kembali)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Kembali</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}</p>
                        </div>
                        @endif
                        @if($peminjaman->keterangan)
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Keterangan</label>
                            <p class="mb-0">{{ $peminjaman->keterangan }}</p>
                        </div>
                        @endif
                        @if($peminjaman->denda > 0)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Denda</label>
                            <p class="mb-0 text-danger fw-bold">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        @if($peminjaman->total_tagihan > 0)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Total Tagihan</label>
                            <p class="mb-0 fw-bold">Rp {{ number_format($peminjaman->total_tagihan, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.peminjaman.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        
                        @if($peminjaman->status == 'approved')
                            <button type="button" class="btn btn-success" onclick="confirmTake({{ $peminjaman->id }})">
                                <i class="fas fa-check-circle me-1"></i> Konfirmasi Pengambilan
                            </button>
                        @endif
                        
                        @if($peminjaman->status == 'pending')
                            <button type="button" class="btn btn-danger" onclick="cancelBorrowing({{ $peminjaman->id }})">
                                <i class="fas fa-times me-1"></i> Batalkan Peminjaman
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmTake(id) {
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
}

function cancelBorrowing(id) {
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
                        window.location.href = '{{ route("user.peminjaman.index") }}';
                    });
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
}
</script>
@endsection