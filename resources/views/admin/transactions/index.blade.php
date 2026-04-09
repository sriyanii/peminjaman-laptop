@extends('layouts.app')

@section('title', 'Transaksi & Denda')
@section('header-title', 'Transaksi & Denda')
@section('header-icon', 'fas fa-money-bill-wave')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
@section('content')
<div class="container-fluid">
    <!-- ====================== STATISTIK ====================== -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="stats-number">{{ $total_transaksi ?? 0 }}</h3>
                <p class="stats-label">Total Transaksi/Denda</p>
                <div class="stats-trend text-primary">
                    <i class="fas fa-chart-line me-1"></i>
                    Semua transaksi
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-coins"></i>
                </div>
                <h3 class="stats-number">Rp {{ number_format($total_pendapatan ?? 0, 0, ',', '.') }}</h3>
                <p class="stats-label">Total Pendapatan</p>
                <div class="stats-trend text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    Denda lunas
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="stats-number">Rp {{ number_format($total_denda_belum_lunas ?? 0, 0, ',', '.') }}</h3>
                <p class="stats-label">Denda Belum Lunas</p>
                <div class="stats-trend text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Perlu penagihan
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h3 class="stats-number">{{ $transaksi_pending ?? 0 }}</h3>
                <p class="stats-label">Transaksi Pending</p>
                <div class="stats-trend text-info">
                    <i class="fas fa-sync-alt me-1"></i>
                    Proses verifikasi
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== FILTER DAN PENCARIAN ====================== -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.transactions.index') }}" method="GET" class="row g-3">
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
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ====================== TABEL TRANSAKSI ====================== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Daftar Transaksi & Denda
            </h5>
            <div class="d-flex gap-2">
                <!-- HAPUS TOMBOL KE PENGEMBALIAN - Admin tidak punya menu pengembalian -->
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-file-export me-2"></i>Export
                </button>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
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

            @if(($transaksis ?? $transactions ?? collect())->count() > 0)
                @php
                    $dataTransaksi = $transaksis ?? $transactions ?? collect();
                @endphp
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-light">
                                <th width="50" class="text-center">No</th>
                                <th>Transaksi</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th class="text-end">Total Transaksi</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th class="text-center">Status Transaksi</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center" width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataTransaksi as $index => $item)
                            <tr>
                                <td class="text-center">{{ $dataTransaksi->firstItem() + $index }}</td>
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
                                            <strong>{{ $item->peminjaman->laptop->merk ?? '' }} {{ $item->peminjaman->laptop->model ?? '' }}</strong>
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
                                                   href="{{ route('admin.transactions.show', $item->id) }}">
                                                    <i class="fas fa-eye me-2"></i>Detail
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item text-danger"
                                                        onclick="deleteTransaction({{ $item->id }}, '{{ $item->id }}')">
                                                    <i class="fas fa-trash me-2"></i>Hapus Transaksi
                                                </button>
                                            </li>
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
                        Menampilkan {{ $dataTransaksi->firstItem() }} - {{ $dataTransaksi->lastItem() }} 
                        dari {{ $dataTransaksi->total() }} transaksi
                    </div>
                    <div>
                        {{ $dataTransaksi->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-3">Belum ada transaksi denda</h5>
                    <p class="text-muted mb-4">Belum ada transaksi denda yang tercatat.</p>
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
                    <i class="fas fa-file-pdf me-2"></i>Export Data Transaksi ke PDF
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.transactions.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Hidden input untuk format PDF -->
                    <input type="hidden" name="format" value="pdf">
                    
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
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Delete transaction
    function deleteTransaction(id, kode) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Transaksi <strong>#${kode}</strong> akan dihapus permanen!<br>
                  <small class="text-danger">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/transactions') }}/${id}`;
                form.style.display = 'none';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // Notifikasi dari session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endpush