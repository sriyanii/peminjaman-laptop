@extends('layouts.app')

@section('title', 'Manajemen Peminjaman')
@section('header-icon', 'fas fa-hand-holding')
@section('header-title', 'Manajemen Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <!-- STATISTIK -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stats-number">{{ $statistics['total'] ?? 0 }}</div>
                <div class="stats-label">Total Peminjaman</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ $statistics['pending'] ?? 0 }}</div>
                <div class="stats-label">Menunggu</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stats-number">{{ $statistics['active'] ?? 0 }}</div>
                <div class="stats-label">Sedang Dipinjam</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ $statistics['returned'] ?? 0 }}</div>
                <div class="stats-label">Selesai</div>
            </div>
        </div>
    </div>

    <!-- Alert Session -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- TABEL PEMINJAMAN -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Daftar Peminjaman
                </h5>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('admin.borrowings.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Cari peminjaman..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filter Status -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.borrowings.index') }}" 
                           class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.borrowings.index', ['status' => 'pending']) }}" 
                           class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                            Menunggu
                        </a>
                        <a href="{{ route('admin.borrowings.index', ['status' => 'approved']) }}" 
                           class="btn btn-outline-info {{ request('status') == 'approved' ? 'active' : '' }}">
                            Disetujui
                        </a>
                        <a href="{{ route('admin.borrowings.index', ['status' => 'aktif']) }}" 
                           class="btn btn-outline-primary {{ request('status') == 'aktif' ? 'active' : '' }}">
                            Dipinjam
                        </a>
                        <a href="{{ route('admin.borrowings.index', ['status' => 'selesai']) }}" 
                           class="btn btn-outline-success {{ request('status') == 'selesai' ? 'active' : '' }}">
                            Selesai
                        </a>
                        <a href="{{ route('admin.borrowings.index', ['status' => 'ditolak']) }}" 
                           class="btn btn-outline-danger {{ request('status') == 'ditolak' ? 'active' : '' }}">
                            Ditolak
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Tambahan -->
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.borrowings.index') }}" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
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
                            <label for="peminjam" class="form-label">Peminjam</label>
                            <input type="text" class="form-control" id="peminjam" name="peminjam" 
                                   value="{{ request('peminjam') }}" placeholder="Nama atau email peminjam">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.borrowings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="borrowings-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Laptop</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $borrowing->id }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $borrowing->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($borrowing->created_at)) . '-' . str_pad($borrowing->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $borrowing->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $borrowing->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                @php
                                    $alat = $borrowing->laptop ?? $borrowing->laptop;
                                @endphp
                                
                                @if($alat)
                                    <div class="fw-bold">{{ $alat->nama_alat ?? ($alat->merk ?? '') . ' ' . ($alat->model ?? '') }}</div>
                                    <small class="text-muted">
                                        SN: {{ $alat->serial_number ?? ($alat->kode_alat ?? '-') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($borrowing->tanggal_pinjam)->format('d/m/Y') }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($borrowing->tanggal_kembali)
                                    {{ \Carbon\Carbon::parse($borrowing->tanggal_kembali)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
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
                                        'batal' => 'secondary',
                                        'terlambat' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'aktif' => 'Dipinjam',
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                        'batal' => 'Batal',
                                        'terlambat' => 'Terlambat'
                                    ];
                                    
                                    $isTerlambat = in_array($borrowing->status, ['approved', 'aktif']) && 
                                                   $borrowing->tanggal_kembali_rencana && 
                                                   \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana)->isPast() &&
                                                   !$borrowing->tanggal_kembali;
                                @endphp
                                <span class="badge bg-{{ $statusColors[$borrowing->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$borrowing->status] ?? ucfirst($borrowing->status) }}
                                </span>
                                
                                @if($isTerlambat)
                                    <br>
                                    <span class="badge bg-danger mt-1">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                    </span>
                                @endif
                                
                                @if($borrowing->denda > 0 && !($borrowing->is_denda_dibayar ?? false))
                                    <br>
                                    <span class="badge bg-warning mt-1">
                                        <i class="fas fa-money-bill-wave me-1"></i>Denda: Rp {{ number_format($borrowing->denda, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" 
                                       class="btn btn-outline-info" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.borrowings.destroy', $borrowing->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus peminjaman {{ $borrowing->kode_peminjaman ?? 'ini' }}? Data yang dihapus tidak dapat dikembalikan!')" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-hand-holding fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada peminjaman</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }} 
                    dari {{ $borrowings->total() }} peminjaman
                </div>
                <div class="pagination-container">
                    {{ $borrowings->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Set min date untuk filter tanggal
        const today = new Date().toISOString().split('T')[0];
        $('#tanggal_mulai').attr('max', today);
        $('#tanggal_selesai').attr('max', today);
        
        // Validasi tanggal
        $('#tanggal_mulai').change(function() {
            const startDate = $(this).val();
            $('#tanggal_selesai').attr('min', startDate);
            
            if ($('#tanggal_selesai').val() && $('#tanggal_selesai').val() < startDate) {
                $('#tanggal_selesai').val('');
            }
        });
        
        $('#tanggal_selesai').change(function() {
            const endDate = $(this).val();
            $('#tanggal_mulai').attr('max', endDate);
            
            if ($('#tanggal_mulai').val() && $('#tanggal_mulai').val() > endDate) {
                $('#tanggal_mulai').val('');
            }
        });

        // Alert auto dismiss
        setTimeout(function() {
            $('.alert-dismissible').fadeTo(500, 0).slideUp(500, function(){
                $(this).alert('close');
            });
        }, 5000);
    });
    
    // Print report function
    function printReport() {
        let currentUrl = window.location.href;
        let printWindow = window.open(currentUrl, '_blank');
        
        printWindow.onload = function() {
            let style = printWindow.document.createElement('style');
            style.textContent = `
                @media print {
                    .btn, .btn-group, .stats-card, .pagination, form,
                    .alert, .d-flex.gap-2, .btn-group-sm, .card-title .d-flex {
                        display: none !important;
                    }
                    .card {
                        box-shadow: none !important;
                        border: 1px solid #ddd !important;
                    }
                    .table {
                        width: 100% !important;
                    }
                }
            `;
            printWindow.document.head.appendChild(style);
            setTimeout(function() {
                printWindow.print();
            }, 500);
        };
    }
</script>
@endsection