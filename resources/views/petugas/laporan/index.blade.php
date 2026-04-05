@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4 g-3">

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

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('petugas.laporan') }}" class="row g-3">
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
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Peminjaman</h6>
                                    <h2 class="mb-0">{{ $total_peminjaman }}</h2>
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
                                    <h2 class="mb-0">Rp {{ number_format($total_denda, 0, ',', '.') }}</h2>
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
                                    <h6 class="card-title">Rata-rata Waktu</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $avgDays = $laporan->avg(function($item) {
                                                return \Carbon\Carbon::parse($item->tanggal_pinjam)
                                                    ->diffInDays($item->tanggal_kembali);
                                            });
                                        @endphp
                                        {{ round($avgDays) }} hari
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
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Peminjam</th>
                                    <th>Laptop</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                    <th>Denda</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan as $item)
                                @php
                                    $durasi = \Carbon\Carbon::parse($item->tanggal_pinjam)
                                        ->diffInDays($item->tanggal_kembali);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->kode_peminjaman }}</span></td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>{{ $item->laptop->merk ?? '-' }} {{ $item->laptop->model ?? '' }}</td>
                                    <td>{{ date('d/m/Y', strtotime($item->tanggal_pinjam)) }}</td>
                                    <td>{{ $item->tanggal_kembali ? date('d/m/Y', strtotime($item->tanggal_kembali)) : '-' }}</td>
                                    <td>{{ $durasi }} hari</td>
                                    <td>
                                        @if($item->status == 'selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($item->status == 'disetujui')
                                            <span class="badge bg-primary">Disetujui</span>
                                        @elseif($item->status == 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->denda > 0)
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
                                    <td colspan="10" class="text-center">
                                        <div class="alert alert-info">
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
                    @if($laporan->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $laporan->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        // Create worksheet from HTML table
        var table = document.getElementById('laporanTable');
        var workbook = XLSX.utils.table_to_book(table, {sheet: "Laporan"});
        
        // Add summary data
        var summaryData = [
            ["LAPORAN PEMINJAMAN LAPTOP"],
            ["Tanggal Export", new Date().toLocaleDateString('id-ID')],
            ["Total Peminjaman", {{ $total_peminjaman }}],
            ["Total Denda", "Rp {{ number_format($total_denda, 0, ',', '.') }}"],
            [""],
            ["Detail Laporan:"]
        ];
        
        var wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
        
        // Create new workbook with summary sheet
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, wsSummary, "Ringkasan");
        XLSX.utils.book_append_sheet(wb, workbook.Sheets["Laporan"], "Detail");
        
        // Generate filename with current date
        var date = new Date().toISOString().slice(0, 10);
        var filename = "Laporan_Peminjaman_" + date + ".xlsx";
        
        // Save file
        XLSX.writeFile(wb, filename);
    }
</script>
@endsection