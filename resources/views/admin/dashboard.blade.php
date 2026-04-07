@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header-icon', 'fas fa-tachometer-alt')
@section('header-title', 'Dashboard Admin')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <!-- ====================== STATISTIK UTAMA ====================== -->
    <div class="row mb-4 g-3">
        <!-- Card: Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ $total_users ?? 0 }}</div>
                <div class="stats-label">Total Users</div>
                <div class="stats-trend text-info">
                    <i class="fas fa-user-tie me-1"></i>
                    {{ $total_petugas ?? 0 }} Petugas
                </div>
            </div>
        </div>
        
        <!-- Card: Total Laptop -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stats-number">{{ $total_laptop ?? 0 }}</div>
                <div class="stats-label">Total Laptop</div>
                <div class="stats-trend text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ $laptop_tersedia ?? 0 }} Tersedia
                </div>
            </div>
        </div>
        
        <!-- Card: Total Peminjaman -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="stats-number">{{ $total_peminjaman ?? 0 }}</div>
                <div class="stats-label">Total Peminjaman</div>
                <div class="stats-trend text-warning">
                    <i class="fas fa-clock me-1"></i>
                    {{ $peminjaman_aktif ?? 0 }} Aktif
                </div>
            </div>
        </div>
        
        <!-- Card: Laptop Rusak/Maintenance -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number">{{ $laptop_rusak ?? 0 }}</div>
                <div class="stats-label">Laptop Rusak</div>
                <div class="stats-trend text-danger">
                    <i class="fas fa-tools me-1"></i>
                    Perlu maintenance
                </div>
            </div>
        </div>
    </div>
    
    <!-- ====================== STATISTIK PEMINJAMAN ====================== -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div>
                    <span class="text-muted small">Menunggu</span>
                    <h4 class="mb-0">{{ $peminjaman_pending ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="fas fa-hand-holding text-primary"></i>
                </div>
                <div>
                    <span class="text-muted small">Dipinjam</span>
                    <h4 class="mb-0">{{ $peminjaman_aktif ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div>
                    <span class="text-muted small">Selesai</span>
                    <h4 class="mb-0">{{ $peminjaman_selesai ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                <div>
                    <span class="text-muted small">Terlambat</span>
                    <h4 class="mb-0">{{ $peminjaman_terlambat ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ====================== GRAFIK BATANG (PASTI MUNCUL) ====================== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-primary me-2"></i>
                            Aktivitas Peminjaman 7 Hari Terakhir
                        </h5>
                        <span class="badge bg-primary">Total: {{ array_sum($barData ?? []) }} peminjaman</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Data dummy jika tidak ada data dari controller -->
                    @php
                        $labels = $barLabels ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                        $data = $barData ?? [4, 7, 5, 6, 8, 3, 2];
                    @endphp
                    
                    <!-- Canvas untuk Grafik Batang -->
                    <canvas id="myBarChart" style="height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ====================== QUICK ACTIONS ====================== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-bolt text-primary me-2"></i>
                        Aksi Cepat
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.borrowings.index', ['status' => 'pending']) }}" class="btn btn-outline-warning">
                            <i class="fas fa-clock me-2"></i>Setujui Peminjaman
                        </a>
                        <a href="{{ route('admin.borrowings.index', ['status' => 'aktif']) }}" class="btn btn-outline-primary">
                            <i class="fas fa-undo me-2"></i>Proses Pengembalian
                        </a>
                        <a href="{{ route('admin.borrowings.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-2"></i>Peminjaman Baru
                        </a>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-money-bill-wave me-2"></i>Transaksi Denda
                        </a>
                        <a href="{{ route('admin.laptops.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-laptop me-2"></i>Data Laptop
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ====================== PEMINJAMAN TERBARU ====================== -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-alt text-primary me-2"></i>
                        Peminjaman Terbaru
                    </h5>
                    <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
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
                                        <div class="fw-bold">{{ $peminjaman->user->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $peminjaman->user->email ?? '' }}</small>
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
                                        <span class="badge bg-{{ $statusColors[$peminjaman->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$peminjaman->status] ?? ucfirst($peminjaman->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.borrowings.show', $peminjaman->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">Belum ada data peminjaman</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller (dengan fallback data dummy)
        const labels = {!! json_encode($barLabels ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']) !!};
        const data = {!! json_encode($barData ?? [4, 7, 5, 6, 8, 3, 2]) !!};
        
        console.log('Labels:', labels);
        console.log('Data:', data);
        
        // Dapatkan elemen canvas
        const canvas = document.getElementById('myBarChart');
        
        if (canvas) {
            const ctx = canvas.getContext('2d');
            
            // Buat grafik batang
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: data,
                        backgroundColor: '#4361ee',
                        borderColor: '#3a56d4',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
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
                                color: '#e9ecef'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Canvas element tidak ditemukan!');
        }
    });
</script>


@endpush