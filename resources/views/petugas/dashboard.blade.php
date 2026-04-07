@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header-icon', 'fas fa-tachometer-alt')
@section('header-title', 'Dashboard Petugas')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/petugas.css') }}">
@endpush
@section('content')
<!-- Stats Row -->
<div class="row g-3 mb-4">
    {{-- Total Peminjaman --}}
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="card-body">
                <i class="fas fa-hand-holding card-icon text-primary"></i>
                <div class="card-title">TOTAL PEMINJAMAN</div>
                <div class="card-value">{{ number_format($total_peminjaman ?? 0, 0, ',', '.') }}</div>
                <div class="card-change positive">
                    <i class="fas fa-arrow-up me-1"></i>
                    {{ $peminjaman_hari_ini ?? 0 }} hari ini
                </div>
            </div>
        </div>
    </div>

    {{-- Sedang Dipinjam --}}
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="card-body">
                <i class="fas fa-clock card-icon text-warning"></i>
                <div class="card-title">SEDANG DIPINJAM</div>
                <div class="card-value">{{ number_format($peminjaman_aktif ?? 0, 0, ',', '.') }}</div>
                <div class="card-change">
                    <i class="fas fa-users me-1"></i>
                    {{ $total_peminjam ?? 0 }} peminjam aktif
                </div>
            </div>
        </div>
    </div>

    {{-- Menunggu Persetujuan --}}
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="card-body">
                <i class="fas fa-hourglass-half card-icon text-info"></i>
                <div class="card-title">MENUNGGU PERSETUJUAN</div>
                <div class="card-value">{{ number_format($peminjaman_pending ?? 0, 0, ',', '.') }}</div>
                <div class="card-change text-info">
                    <i class="fas fa-bell me-1"></i>
                    Perlu ditinjau
                </div>
            </div>
        </div>
    </div>

    {{-- Laptop Tersedia --}}
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="card-body">
                <i class="fas fa-laptop card-icon text-success"></i>
                <div class="card-title">LAPTOP TERSEDIA</div>
                <div class="card-value">{{ number_format($laptop_tersedia ?? 0, 0, ',', '.') }}</div>
                <div class="card-change text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    Siap dipinjam
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grafik --}}
<div class="row g-3 mb-4">
    {{-- Bar Chart - Aktivitas Peminjaman --}}
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Aktivitas Peminjaman 7 Hari Terakhir
                </h5>
                <span class="badge bg-primary">Total: {{ array_sum($barData ?? [0,0,0,0,0,0,0]) }} peminjaman</span>
            </div>
            <div class="card-body">
                <div style="height: 300px; width: 100%;">
                    <canvas id="barChart" style="height: 100%; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Donut Chart - Status Peminjaman --}}
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Status Peminjaman
                </h5>
            </div>
            <div class="card-body">
                <div style="height: 200px; width: 100%; margin-bottom: 20px;">
                    <canvas id="donutChart" style="height: 100%; width: 100%;"></canvas>
                </div>
                
                <!-- Legend -->
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #4361ee;"></span>
                            <span class="legend-label">Dipinjam</span>
                            <span class="legend-value ms-auto">{{ $peminjaman_aktif ?? 0 }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #10b981;"></span>
                            <span class="legend-label">Selesai</span>
                            <span class="legend-value ms-auto">{{ $peminjaman_selesai ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #f59e0b;"></span>
                            <span class="legend-label">Menunggu</span>
                            <span class="legend-value ms-auto">{{ $peminjaman_pending ?? 0 }}</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #ef4444;"></span>
                            <span class="legend-label">Terlambat</span>
                            <span class="legend-value ms-auto">{{ $peminjaman_terlambat ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<!-- <div class="row g-3 mb-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bolt text-primary me-2"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('petugas.peminjaman.index', ['status' => 'pending']) }}" class="btn btn-outline-warning">
                        <i class="fas fa-clock me-2"></i>Setujui Peminjaman
                    </a>

                    <a href="{{ route('petugas.peminjaman.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus me-2"></i>Peminjaman Baru
                    </a>
                    <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-money-bill-wave me-2"></i>Transaksi Denda
                    </a>
                    <a href="{{ route('petugas.alat.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-laptop me-2"></i>Data Laptop
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- Recent Bookings -->
<div class="row g-3">
    <div class="col-12">
        <div class="chart-card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-list-alt text-primary me-2"></i>
                    Peminjaman Terbaru
                </h5>
                <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Laptop</th>
                                <th>Tanggal Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_peminjaman ?? [] as $peminjaman)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $peminjaman->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                            <span>{{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold">{{ $peminjaman->user->name ?? '-' }}</span>
                                            <small class="text-muted d-block">{{ $peminjaman->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($peminjaman->laptop)
                                        <div class="fw-bold">{{ $peminjaman->laptop->merk ?? '' }} {{ $peminjaman->laptop->model ?? '' }}</div>
                                        <small class="text-muted">SN: {{ $peminjaman->laptop->serial_number ?? '-' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($peminjaman->tanggal_kembali_rencana)
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}
                                        @if($peminjaman->status == 'aktif' && !$peminjaman->tanggal_kembali && \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->isPast())
                                            <br><span class="badge bg-danger mt-1">Terlambat</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'aktif' => 'primary',
                                            'selesai' => 'success',
                                            'ditolak' => 'danger',
                                            'batal' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Menunggu',
                                            'approved' => 'Disetujui',
                                            'aktif' => 'Dipinjam',
                                            'selesai' => 'Selesai',
                                            'ditolak' => 'Ditolak',
                                            'batal' => 'Batal'
                                        ];
                                    @endphp
                                    <span class="status-badge bg-{{ $statusColors[$peminjaman->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('petugas.peminjaman.index', ['id' => $peminjaman->id]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data peminjaman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - memulai inisialisasi chart...');
        
        // ========== DATA DARI CONTROLLER (DENGAN FALLBACK) ==========
        const barLabels = {!! json_encode($barLabels ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']) !!};
        const barData = {!! json_encode($barData ?? [4, 7, 5, 6, 8, 3, 2]) !!};
        const donutData = {!! json_encode($donutData ?? [6, 5, 2, 5]) !!}; // Dipinjam, Selesai, Menunggu, Terlambat
        
        console.log('Bar Data:', barData);
        console.log('Donut Data:', donutData);
        
        // ========== BAR CHART ==========
        const barCanvas = document.getElementById('barChart');
        if (barCanvas) {
            try {
                new Chart(barCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: barLabels,
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: barData,
                            backgroundColor: '#4361ee',
                            borderRadius: 6,
                            barPercentage: 0.7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw + ' peminjaman';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { 
                                    stepSize: 1,
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
                console.log('Bar chart berhasil dibuat');
            } catch (error) {
                console.error('Error membuat bar chart:', error);
            }
        } else {
            console.error('Canvas barChart tidak ditemukan!');
        }
        
        // ========== DONUT CHART ==========
        const donutCanvas = document.getElementById('donutChart');
        if (donutCanvas) {
            try {
                new Chart(donutCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Dipinjam', 'Selesai', 'Menunggu', 'Terlambat'],
                        datasets: [{
                            data: donutData,
                            backgroundColor: ['#4361ee', '#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw + ' peminjaman';
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
                console.log('Donut chart berhasil dibuat');
            } catch (error) {
                console.error('Error membuat donut chart:', error);
            }
        } else {
            console.error('Canvas donutChart tidak ditemukan!');
        }
    });

    // Handle window resize untuk memastikan canvas tetap responsif
    window.addEventListener('resize', function() {
        const charts = ['barChart', 'donutChart'];
        charts.forEach(function(chartId) {
            const canvas = document.getElementById(chartId);
            if (canvas && canvas.chart) {
                canvas.chart.resize();
            }
        });
    });
</script>
@endpush