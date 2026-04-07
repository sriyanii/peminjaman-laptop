@extends('layouts.app')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-peminjaman.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4 g-3">
        <!-- Main content -->
        <div class="col-12">


            <!-- Video Section - Lebih modern dengan desain minimalis -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="video-wrapper position-relative rounded-4 overflow-hidden" style="height: 200px; background: linear-gradient(145deg, #1a1a1a, #2d2d2d);">
                        <!-- Video with Sound -->
                        <video autoplay loop controls playsinline class="video-player">
                            <source src="{{ asset('video/videolaptop.mp4') }}" type="video/mp4">
                            Browser Anda tidak mendukung tag video.
                        </video>
                        
                        <!-- Minimal Sound Indicator -->
                        <div class="sound-badge">
                            <i class="fas fa-volume-up me-1"></i>
                            <span>Audio Available</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Cards - Layout lebih modern dengan spacing yang pas -->
            <div class="row mb-5 g-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-primary">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $total_laptop ?? 0 }}</div>
                            <div class="stat-label">Alat Tersedia</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $peminjaman_aktif ?? 0 }}</div>
                            <div class="stat-label">Peminjaman Aktif</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $peminjaman_selesai ?? 0 }}</div>
                            <div class="stat-label">Peminjaman Selesai</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-info">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $peminjaman_pending ?? 0 }}</div>
                            <div class="stat-label">Menunggu Persetujuan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peminjaman Terbaru - Dengan desain table yang lebih modern -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-semibold mb-1">
                                        <i class="fas fa-history me-2 text-primary"></i>Peminjaman Terbaru
                                    </h5>
                                    <p class="text-muted small mb-0">Daftar 5 peminjaman terakhir</p>
                                </div>
                                <a href="{{ route('user.peminjaman.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    Lihat Semua
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            @if($peminjaman_terbaru && $peminjaman_terbaru->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 rounded-start">Kode</th>
                                            <th class="border-0">Alat</th>
                                            <th class="border-0">Tanggal Pinjam</th>
                                            <th class="border-0">Tanggal Kembali</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0 rounded-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($peminjaman_terbaru as $pinjam)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark py-2 px-3">#{{ $pinjam->kode_peminjaman ?? $pinjam->id }}</span>
                                            </td>
                                            <td>
                                                @if($pinjam->laptop)
                                                    <span class="fw-medium">{{ $pinjam->laptop->nama_alat ?? 'Alat' }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $pinjam->tanggal_pinjam ? date('d/m/Y', strtotime($pinjam->tanggal_pinjam)) : '-' }}</td>
                                            <td>{{ $pinjam->tanggal_kembali ? date('d/m/Y', strtotime($pinjam->tanggal_kembali)) : '-' }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'menunggu' => 'warning',
                                                        'approved' => 'success',
                                                        'disetujui' => 'success',
                                                        'aktif' => 'primary',
                                                        'selesai' => 'info',
                                                        'ditolak' => 'danger',
                                                        'batal' => 'secondary',
                                                        'terlambat' => 'danger'
                                                    ];
                                                    $badgeColor = $statusColors[$pinjam->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} px-3 py-2 rounded-pill">
                                                    {{ ucfirst($pinjam->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('user.peminjaman.show', $pinjam->id) }}" 
                                                   class="btn btn-sm btn-light rounded-circle" style="width: 36px; height: 36px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h6 class="fw-semibold">Belum Ada Peminjaman</h6>
                                    <p class="text-muted small">Anda belum melakukan peminjaman apapun</p>
                                    <a href="{{ route('user.alat') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">
                                        Pinjam Alat Sekarang
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection