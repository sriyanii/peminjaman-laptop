@extends('layouts.app')

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

            <!-- Quick Actions - Desain lebih elegan -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="section-title mb-4">
                        <h5 class="fw-semibold mb-0">
                            <i class="fas fa-bolt me-2 text-primary"></i>Aksi Cepat
                        </h5>
                        <p class="text-muted small">Pilih menu untuk memulai</p>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('user.alat') }}" class="action-card action-card-primary">
                                <div class="action-icon">
                                    <i class="fas fa-laptop"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Pinjam Alat</span>
                                    <small class="action-desc">Booking alat tersedia</small>
                                </div>
                                <i class="fas fa-arrow-right action-arrow"></i>
                            </a>
                        </div>
                        
                        <div class="col-md-3">
                            <a href="{{ route('user.peminjaman.index') }}" class="action-card action-card-warning">
                                <div class="action-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Lihat Peminjaman</span>
                                    <small class="action-desc">Cek status peminjaman</small>
                                </div>
                                <i class="fas fa-arrow-right action-arrow"></i>
                            </a>
                        </div>
                        
                        <div class="col-md-3">
                            <a href="{{ route('user.pengembalian.index') }}" class="action-card action-card-success">
                                <div class="action-icon">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Pengembalian</span>
                                    <small class="action-desc">Kembalikan alat</small>
                                </div>
                                <i class="fas fa-arrow-right action-arrow"></i>
                            </a>
                        </div>
                        
                        <div class="col-md-3">
                            <a href="{{ route('user.history') }}" class="action-card action-card-info">
                                <div class="action-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Riwayat</span>
                                    <small class="action-desc">Lihat histori peminjaman</small>
                                </div>
                                <i class="fas fa-arrow-right action-arrow"></i>
                            </a>
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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Modern Typography */
    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    /* Video Styles */
    .video-wrapper {
        position: relative;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .video-player {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.95;
    }
    
    .sound-badge {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    /* Stat Cards - Modern Minimalis */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.03);
        height: 100%;
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border-color: transparent;
    }
    
    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .stat-icon-primary {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
    }
    
    .stat-icon-warning {
        background: rgba(255, 193, 7, 0.08);
        color: #ffc107;
    }
    
    .stat-icon-success {
        background: rgba(25, 135, 84, 0.08);
        color: #198754;
    }
    
    .stat-icon-info {
        background: rgba(13, 202, 240, 0.08);
        color: #0dcaf0;
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 4px;
        color: #1a1a1a;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }
    
    /* Action Cards - Modern */
    .action-card {
        display: flex;
        align-items: center;
        padding: 20px;
        background: white;
        border-radius: 14px;
        text-decoration: none;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
        gap: 15px;
    }
    
    .action-card:hover {
        transform: translateX(5px);
        border-color: transparent;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    
    .action-card-primary .action-icon {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
    }
    
    .action-card-warning .action-icon {
        background: rgba(255, 193, 7, 0.08);
        color: #ffc107;
    }
    
    .action-card-success .action-icon {
        background: rgba(25, 135, 84, 0.08);
        color: #198754;
    }
    
    .action-card-info .action-icon {
        background: rgba(13, 202, 240, 0.08);
        color: #0dcaf0;
    }
    
    .action-text {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .action-title {
        color: #1a1a1a;
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 3px;
    }
    
    .action-desc {
        color: #8f9bb3;
        font-size: 12px;
    }
    
    .action-arrow {
        color: #cbd5e0;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .action-card:hover .action-arrow {
        color: #0d6efd;
        transform: translateX(3px);
    }
    
    /* Section Title */
    .section-title {
        padding-left: 4px;
    }
    
    /* Table Styles */
    .table thead th {
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 15px 12px;
    }
    
    .table tbody td {
        padding: 15px 12px;
        color: #2d3e50;
        font-size: 14px;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    
    .badge.bg-opacity-10 {
        font-weight: 500;
        font-size: 12px;
    }
    
    /* Empty State */
    .empty-state {
        max-width: 300px;
        margin: 0 auto;
    }
    
    /* Card */
    .card {
        border-radius: 20px;
    }
    
    /* Button */
    .btn-light {
        background: #f8fafd;
        border: none;
        color: #4a5b6e;
    }
    
    .btn-light:hover {
        background: #e9ecef;
        color: #0d6efd;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stat-card {
            padding: 16px;
        }
        
        .action-card {
            padding: 16px;
        }
        
        .video-wrapper {
            height: 160px !important;
        }
    }
</style>
@endpush
@endsection