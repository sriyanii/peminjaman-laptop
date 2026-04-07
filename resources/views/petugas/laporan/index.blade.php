@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('header-icon', 'fas fa-chart-bar')
@section('header-title', 'Laporan Peminjaman')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/petugas.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4 g-3">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chart-bar me-2"></i>Laporan Peminjaman
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('petugas.laporan') }}" class="row g-3" id="filterForm">
                <div class="col-md-3">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                           value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-3">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                           value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Dipinjam</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Peminjaman</h6>
                            <h2 class="mb-0">{{ number_format($total_peminjaman ?? 0) }}</h2>
                        </div>
                        <i class="fas fa-laptop fa-2x align-self-center"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Denda</h6>
                            <h2 class="mb-0">Rp {{ number_format($total_denda ?? 0, 0, ',', '.') }}</h2>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x align-self-center"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Rata-rata Durasi</h6>
                            <h4 class="mb-0">
                                @php
                                    $avgDays = 0;
                                    if(isset($laporan) && $laporan->count() > 0) {
                                        $totalDays = 0;
                                        $count = 0;
                                        foreach($laporan as $item) {
                                            if($item->tanggal_kembali) {
                                                $totalDays += \Carbon\Carbon::parse($item->tanggal_pinjam)
                                                    ->diffInDays($item->tanggal_kembali);
                                                $count++;
                                            }
                                        }
                                        $avgDays = $count > 0 ? round($totalDays / $count) : 0;
                                    }
                                @endphp
                                {{ $avgDays }} hari
                            </h4>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x align-self-center"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Detail Laporan Peminjaman</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="laporanTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Laptop</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Rencana</th>
                            <th>Tanggal Kembali</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan ?? [] as $item)
                        @php
                            $durasi = 0;
                            if($item->tanggal_kembali) {
                                $durasi = \Carbon\Carbon::parse($item->tanggal_pinjam)
                                    ->diffInDays($item->tanggal_kembali);
                            }
                            
                            $statusColors = [
                                'selesai' => 'success',
                                'approved' => 'primary',
                                'ditolak' => 'danger',
                                'aktif' => 'warning',
                                'pending' => 'secondary'
                            ];
                            $statusColor = $statusColors[$item->status] ?? 'secondary';
                            $statusLabels = [
                                'selesai' => 'Selesai',
                                'approved' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'aktif' => 'Dipinjam',
                                'pending' => 'Menunggu'
                            ];
                            $statusLabel = $statusLabels[$item->status] ?? ucfirst($item->status);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-secondary">{{ $item->kode_peminjaman ?? 'PINJ-' . $item->id }}</span></td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ $item->laptop->merk ?? '-' }} {{ $item->laptop->model ?? '' }}</td>
                            <td>{{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->tanggal_kembali_rencana ? \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $durasi }} hari</td>
                            <td><span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span></td>
                            <td>
                                @if(($item->denda ?? 0) > 0)
                                    <span class="badge bg-danger">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-success">Rp 0</span>
                                @endif
                            </td>
                            <td>
                                @if($item->catatan_pengembalian)
                                    <button type="button" class="btn btn-sm btn-info" 
                                            data-bs-toggle="popover" 
                                            data-bs-title="Catatan Pengembalian"
                                            data-bs-content="{{ $item->catatan_pengembalian }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tidak ada data laporan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($laporan) && $laporan->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $laporan->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Set min/max dates for filter
    const today = new Date().toISOString().split('T')[0];
    $('#tanggal_mulai').attr('max', today);
    $('#tanggal_selesai').attr('max', today);
    
    $('#tanggal_mulai').change(function() {
        $('#tanggal_selesai').attr('min', $(this).val());
    });
    
    $('#tanggal_selesai').change(function() {
        $('#tanggal_mulai').attr('max', $(this).val());
    });
});

function exportToExcel() {
    // Get the table
    var table = document.getElementById('laporanTable');
    
    // Clone the table to avoid modifying the original
    var cloneTable = table.cloneNode(true);
    
    // Remove action buttons from clone
    $(cloneTable).find('button').remove();
    
    // Create worksheet from HTML table
    var workbook = XLSX.utils.book_new();
    var worksheet = XLSX.utils.table_to_sheet(cloneTable, {sheet: "Laporan Peminjaman"});
    
    // Add summary data at the top
    XLSX.utils.sheet_add_aoa(worksheet, [
        ["LAPORAN PEMINJAMAN LAPTOP"],
        ["Tanggal Export", new Date().toLocaleDateString('id-ID')],
        ["Total Peminjaman", "{{ number_format($total_peminjaman ?? 0) }}"],
        ["Total Denda", "Rp {{ number_format($total_denda ?? 0, 0, ',', '.') }}"],
        [""],
        ["Detail Laporan:"]
    ], {origin: "A1"});
    
    // Add to workbook
    XLSX.utils.book_append_sheet(workbook, worksheet, "Laporan Peminjaman");
    
    // Generate filename with current date
    var date = new Date().toISOString().slice(0, 10);
    var filename = "Laporan_Peminjaman_" + date + ".xlsx";
    
    // Save file
    XLSX.writeFile(workbook, filename);
}
</script>
@endsection