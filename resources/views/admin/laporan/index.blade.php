@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('header-icon', 'fas fa-chart-line')
@section('header-title', 'Laporan Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12">
            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Laptop</h5>
                            <h2 class="mb-0">{{ $total_laptops ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total User</h5>
                            <h2 class="mb-0">{{ $total_users ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Peminjaman</h5>
                            <h2 class="mb-0">{{ $total_peminjaman ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Denda</h5>
                            <h2 class="mb-0">Rp {{ number_format($total_transaksi ?? 0, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.laporan') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Laporan -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Peminjaman</h5>
                    <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                </div>
                <div class="card-body">
                    @if(isset($borrowings) && $borrowings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Peminjam</th>
                                        <th>Alat</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Durasi</th>
                                        <th>Status</th>
                                        <th>Denda</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($borrowings as $borrowing)
                                    <tr>
                                        <td>#{{ $borrowing->id }}</td>
                                        <td>
                                            <div>{{ $borrowing->user->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $borrowing->user->email ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $borrowing->laptop->merk ?? '-' }} {{ $borrowing->laptop->model ?? '' }}</div>
                                            <small class="text-muted">SN: {{ $borrowing->laptop->serial_number ?? '-' }}</small>
                                        </td>
                                        <td>
                                            {{ $borrowing->tanggal_pinjam ? \Carbon\Carbon::parse($borrowing->tanggal_pinjam)->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            {{ $borrowing->tanggal_kembali ? \Carbon\Carbon::parse($borrowing->tanggal_kembali)->format('d/m/Y H:i') : '-' }}
                                            @if($borrowing->status === 'selesai' && $borrowing->tanggal_dikembalikan)
                                                <br>
                                                <small class="text-success">Dikembalikan: {{ \Carbon\Carbon::parse($borrowing->tanggal_dikembalikan)->format('d/m/Y H:i') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $start = \Carbon\Carbon::parse($borrowing->tanggal_pinjam);
                                                $end = $borrowing->tanggal_kembali 
                                                    ? \Carbon\Carbon::parse($borrowing->tanggal_kembali) 
                                                    : \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana);
                                                $days = $start->diffInDays($end);
                                            @endphp
                                            {{ $days }} hari
                                            @if($borrowing->tanggal_kembali_rencana && \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana)->isPast() && !$borrowing->tanggal_kembali)
                                                <br>
                                                <small class="text-danger">Terlambat</small>
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
                                                ];
                                                $statusText = [
                                                    'pending' => 'Menunggu',
                                                    'approved' => 'Disetujui',
                                                    'aktif' => 'Aktif',
                                                    'selesai' => 'Selesai',
                                                    'ditolak' => 'Ditolak',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$borrowing->status] ?? 'secondary' }}">
                                                {{ $statusText[$borrowing->status] ?? $borrowing->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($borrowing->denda > 0)
                                                <span class="badge bg-warning">
                                                    Rp {{ number_format($borrowing->denda, 0, ',', '.') }}
                                                </span>
                                                @if($borrowing->is_denda_dibayar == 0)
                                                    <br>
                                                    <small class="text-danger">Belum dibayar</small>
                                                @endif
                                            @else
                                                <span class="badge bg-success">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($borrowings->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $borrowings->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                            <h5>Tidak Ada Data Peminjaman</h5>
                            <p class="text-muted">Belum ada data peminjaman yang tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        const table = document.getElementById('reportTable');
        const wb = XLSX.utils.table_to_book(table, { sheet: "Laporan Peminjaman", raw: true });
        XLSX.writeFile(wb, `laporan_peminjaman_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.xlsx`);
    }
</script>
@endsection