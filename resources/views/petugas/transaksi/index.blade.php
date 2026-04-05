@extends('layouts.app')

@section('title', 'Transaksi & Denda')
@section('header-title', 'Transaksi & Denda')
@section('header-icon', 'fas fa-money-bill-wave')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <!-- Statistik Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="stats-number">{{ $total_transaksi }}</h3>
                <p class="stats-label">Total Transaksi/Denda</p>
                <div class="stats-trend text-primary">
                    <i class="fas fa-chart-line me-1"></i>
                    Semua transaksi
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-coins"></i>
                </div>
                <h3 class="stats-number">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                <p class="stats-label">Total Pendapatan</p>
                <div class="stats-trend text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    Denda lunas
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="stats-number">Rp {{ number_format($total_denda_belum_lunas, 0, ',', '.') }}</h3>
                <p class="stats-label">Denda Belum Lunas</p>
                <div class="stats-trend text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Perlu penagihan
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h3 class="stats-number">{{ $transaksi_pending }}</h3>
                <p class="stats-label">Transaksi Pending</p>
                <div class="stats-trend text-info">
                    <i class="fas fa-sync-alt me-1"></i>
                    Proses verifikasi
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('petugas.transaksi.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status Pembayaran</label>
                    <select class="form-select shadow-sm" name="status_pembayaran">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" {{ request('status_pembayaran') == 'belum_lunas' ? 'selected' : '' }}>
                            Belum Lunas
                        </option>
                        <option value="sebagian" {{ request('status_pembayaran') == 'sebagian' ? 'selected' : '' }}>
                            Sebagian
                        </option>
                        <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>
                            Lunas
                        </option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status Transaksi</label>
                    <select class="form-select shadow-sm" name="status_transaksi">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status_transaksi') == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="proses_cek" {{ request('status_transaksi') == 'proses_cek' ? 'selected' : '' }}>
                            Proses Cek
                        </option>
                        <option value="selesai" {{ request('status_transaksi') == 'selesai' ? 'selected' : '' }}>
                            Selesai
                        </option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pencarian</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Cari peminjam, kode peminjaman..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Daftar Transaksi & Denda
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-undo me-2"></i>Ke Pengembalian
                </a>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-file-export me-2"></i>Export
                </button>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('show_struk'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="text-center mb-3">
                    <button class="btn btn-success" onclick="showStrukModal({{ session('show_struk') }})">
                        <i class="fas fa-receipt me-2"></i>Lihat Struk Pembayaran
                    </button>
                    <a href="{{ route('petugas.transaksi.struk', session('show_struk')) }}" class="btn btn-outline-success" target="_blank">
                        <i class="fas fa-print me-2"></i>Cetak Struk PDF
                    </a>
                </div>
            @elseif(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($transaksis->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-light">
                                <th width="50" class="text-center">No</th>
                                <th>Transaksi</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th class="text-center">Total Transaksi</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th class="text-center">Status Transaksi</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksis as $index => $item)
                            <tr>
                                <td class="text-center">{{ $transaksis->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-primary">#{{ $item->id }}</strong>
                                        <small class="text-muted">
                                            <i class="fas fa-hashtag me-1"></i>
                                            {{ $item->peminjaman->kode_peminjaman ?? 'N/A' }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                            {{ strtoupper(substr($item->user->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $item->user->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->peminjaman && $item->peminjaman->laptop)
                                        <div>
                                            <strong>{{ $item->peminjaman->laptop->merk }} {{ $item->peminjaman->laptop->model }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                SN: {{ $item->peminjaman->laptop->serial_number ?? '-' }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="badge bg-danger">Data alat tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <strong class="text-dark">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</strong>
                                        @if($item->denda_dibayar > 0)
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Dibayar: Rp {{ number_format($item->denda_dibayar, 0, ',', '.') }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @switch($item->status_pembayaran)
                                        @case('lunas')
                                            <span class="badge bg-success rounded-pill px-3">
                                                <i class="fas fa-check-circle me-1"></i>Lunas
                                            </span>
                                            @break
                                        @case('sebagian')
                                            <span class="badge bg-warning rounded-pill px-3">
                                                <i class="fas fa-exclamation-circle me-1"></i>Sebagian
                                            </span>
                                            @break
                                        @case('belum_lunas')
                                            <span class="badge bg-danger rounded-pill px-3">
                                                <i class="fas fa-clock me-1"></i>Belum Lunas
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary rounded-pill px-3">{{ $item->status_pembayaran }}</span>
                                    @endswitch
                                    <br>
                                    @if($item->status_pembayaran != 'lunas')
                                        <small class="text-muted">
                                            Sisa: Rp {{ number_format($item->total_denda - $item->denda_dibayar, 0, ',', '.') }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @switch($item->status_transaksi)
                                        @case('selesai')
                                            <span class="badge bg-success rounded-pill px-3">
                                                <i class="fas fa-check me-1"></i>Selesai
                                            </span>
                                            @break
                                        @case('proses_cek')
                                            <span class="badge bg-info rounded-pill px-3">
                                                <i class="fas fa-spinner me-1"></i>Proses Cek
                                            </span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning rounded-pill px-3">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary rounded-pill px-3">{{ $item->status_transaksi }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ $item->created_at->format('d/m/Y') }}
                                    </small>
                                    @if($item->waktu_cek)
                                        <br>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($item->waktu_cek)->format('H:i') }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="{{ route('petugas.transaksi.show', $item->id) }}">
                                                    <i class="fas fa-eye me-2"></i>Detail
                                                </a>
                                            </li>
                                            
                                            @if($item->status_pembayaran != 'lunas')
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ route('petugas.transaksi.pembayaran', $item->id) }}">
                                                        <i class="fas fa-credit-card me-2"></i>Pembayaran
                                                    </a>
                                                </li>
                                            @endif
                                            
                                            @if($item->status_transaksi == 'pending' && $item->kondisi_barang == null)
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ route('petugas.transaksi.create', $item->peminjaman_id) }}">
                                                        <i class="fas fa-search me-2"></i>Cek Barang
                                                    </a>
                                                </li>
                                            @endif
                                            
                                            @if($item->status_pembayaran == 'lunas' && $item->status_transaksi == 'selesai')
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-success" 
                                                       href="{{ route('petugas.transaksi.struk', $item->id) }}"
                                                       target="_blank">
                                                        <i class="fas fa-print me-2"></i>Cetak Struk
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button"
                                                            class="dropdown-item text-info"
                                                            onclick="showStrukModal({{ $item->id }})">
                                                        <i class="fas fa-eye me-2"></i>Lihat Struk
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $transaksis->firstItem() }} - {{ $transaksis->lastItem() }} 
                        dari {{ $transaksis->total() }} transaksi
                    </div>
                    <div>
                        {{ $transaksis->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-3">Belum ada transaksi denda</h5>
                    <p class="text-muted mb-4">Transaksi denda akan muncul setelah proses pengembalian alat.</p>
                    <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-primary">
                        <i class="fas fa-undo me-2"></i>Lihat Data Pengembalian
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Export -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-export me-2"></i>Export Data Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('petugas.transaksi.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Format Export</label>
                        <select class="form-select" name="format" required>
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pembayaran</label>
                        <select class="form-select" name="status_pembayaran">
                            <option value="">Semua Status</option>
                            <option value="lunas">Lunas</option>
                            <option value="sebagian">Sebagian</option>
                            <option value="belum_lunas">Belum Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================ MODAL LIHAT STRUK ================ -->
<div class="modal fade" id="strukModal" tabindex="-1" aria-labelledby="strukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="strukModalLabel">
                    <i class="fas fa-receipt text-success me-2"></i>Struk Pembayaran Denda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="strukModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat struk...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <a href="#" id="cetakStrukBtn" class="btn btn-success" target="_blank">
                    <i class="fas fa-print me-2"></i>Cetak PDF
                </a>
            </div>
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
    color: var(--dark);
}

.stats-label {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 5px;
}

.stats-trend {
    font-size: 13px;
    display: flex;
    align-items: center;
}

.table-hover tbody tr:hover {
    background-color: rgba(58, 134, 255, 0.05);
}

.avatar-sm {
    width: 36px;
    height: 36px;
    font-weight: 600;
    font-size: 14px;
}

.badge {
    font-weight: 500;
    padding: 5px 10px;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.dropdown-menu {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
}

.dropdown-item {
    border-radius: 5px;
    margin: 2px 5px;
    width: calc(100% - 10px);
}

.dropdown-item:hover {
    background-color: rgba(58, 134, 255, 0.1);
}

.struk-card {
    background: white;
    border: 1px dashed #0d6efd;
    border-radius: 10px;
    padding: 20px;
    font-family: 'Courier New', monospace;
}

.struk-header {
    text-align: center;
    border-bottom: 1px dashed #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.struk-body {
    margin-bottom: 15px;
}

.struk-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.struk-footer {
    border-top: 1px dashed #000;
    padding-top: 10px;
    margin-top: 10px;
    text-align: center;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto set date ranges
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    if (startDateInput && !startDateInput.value) {
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        startDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
    }
    
    if (endDateInput && !endDateInput.value) {
        endDateInput.value = new Date().toISOString().split('T')[0];
    }
    
    // Export form validation
    const exportForm = document.querySelector('#exportModal form');
    if (exportForm) {
        exportForm.addEventListener('submit', function(e) {
            const startDate = this.querySelector('input[name="start_date"]').value;
            const endDate = this.querySelector('input[name="end_date"]').value;
            
            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai!',
                    confirmButtonColor: '#3a86ff'
                });
            }
        });
    }
    
    // Quick filter buttons
    const quickFilterButtons = document.querySelectorAll('.quick-filter');
    quickFilterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const url = new URL(window.location.href);
            url.searchParams.set('status_pembayaran', status);
            window.location.href = url.toString();
        });
    });
    
    // Refresh button functionality
    const refreshBtn = document.querySelector('#refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            this.disabled = true;
            window.location.reload();
        });
    }
});

// Show struk modal
function showStrukModal(id) {
    document.getElementById('strukModalBody').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat struk...</p>
        </div>
    `;
    
    document.getElementById('cetakStrukBtn').href = `/petugas/transaksi/${id}/struk`;
    
    fetch(`/petugas/transaksi/${id}/struk`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memuat struk');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('strukModalBody').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('strukModalBody').innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p class="mb-0">Gagal memuat struk</p>
                    <small>${error.message}</small>
                </div>
            `;
        });
}

// Auto show struk modal after payment
@if(session('show_struk'))
    setTimeout(function() {
        showStrukModal({{ session('show_struk') }});
    }, 1000);
@endif
</script>
@endpush