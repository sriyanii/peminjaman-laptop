@extends('layouts.app')

@section('title', 'Laporan Lengkap')
@section('header-icon', 'fas fa-chart-bar')
@section('header-title', 'Laporan Lengkap Semua Kegiatan')

@section('content')
<div class="container-fluid">
    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Peminjaman</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ number_format($statistik['total_user']) }}</div>
                <div class="stats-label">Total User</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stats-number">{{ number_format($statistik['total_laptop']) }}</div>
                <div class="stats-label">Total Laptop</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="stats-number">{{ number_format($statistik['total_peminjaman']) }}</div>
                <div class="stats-label">Total Peminjaman</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-money-bill"></i>
                </div>
                <div class="stats-number">Rp {{ number_format($statistik['pendapatan'], 0, ',', '.') }}</div>
                <div class="stats-label">Total Pendapatan</div>
            </div>
        </div>
    </div>

    <!-- Statistik Denda -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stats-number">Rp {{ number_format($statistik['total_denda'], 0, ',', '.') }}</div>
                <div class="stats-label">Total Denda</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">Rp {{ number_format($statistik['denda_lunas'], 0, ',', '.') }}</div>
                <div class="stats-label">Denda Lunas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">Rp {{ number_format($statistik['denda_belum_lunas'], 0, ',', '.') }}</div>
                <div class="stats-label">Denda Belum Lunas</div>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan Peminjaman -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Laporan Peminjaman
                </h5>
                <a href="{{ route('admin.laporan.export', request()->all()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th><th>Kode</th><th>Peminjam</th><th>Laptop</th>
                            <th>Tgl Pinjam</th><th>Tgl Rencana</th><th>Tgl Kembali</th><th>Status</th><th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan_peminjaman as $item)
                        <tr>
                            <td>#{{ $item->id }}</td>
                            <td>{{ $item->kode_peminjaman ?? 'PINJ-' . $item->id }}</td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ $item->laptop->merk ?? '' }} {{ $item->laptop->model ?? '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                            <td>{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @php
                                    $statusColors = ['pending'=>'warning','approved'=>'info','aktif'=>'primary','selesai'=>'success','ditolak'=>'danger'];
                                    $statusColor = $statusColors[$item->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($item->status) }}</span>
                             </td>
                            <td>Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $laporan_peminjaman->links() }}
        </div>
    </div>

    <!-- Tabel Laporan Denda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-receipt me-2 text-primary"></i>
                Laporan Denda
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th><th>Peminjam</th><th>Laptop</th>
                            <th>Total Denda</th><th>Dibayar</th><th>Sisa</th><th>Status</th><th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan_denda as $item)
                        <tr>
                            <td>#{{ $item->id }}</td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ $item->peminjaman->laptop->merk ?? '' }} {{ $item->peminjaman->laptop->model ?? '' }}</td>
                            <td class="text-danger">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($item->denda_dibayar, 0, ',', '.') }}</td>
                            <td class="text-warning">Rp {{ number_format($item->total_denda - $item->denda_dibayar, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusColors = ['lunas'=>'success','belum_lunas'=>'danger','sebagian_lunas'=>'warning'];
                                    $statusColor = $statusColors[$item->status_pembayaran] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $item->status_pembayaran)) }}</span>
                             </td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $laporan_denda->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    height: 100%;
    transition: all 0.3s ease;
    border: none;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 15px;
}

.stats-number {
    font-size: 28px;
    font-weight: 700;
    margin: 10px 0;
    color: #2c3e50;
}

.stats-label {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 5px;
}

.table th {
    font-weight: 600;
    color: #495057;
}
</style>
@endpush