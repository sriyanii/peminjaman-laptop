@extends('layouts.app')

@section('title', 'Manajemen Transaksi')
@section('header-icon', 'fas fa-money-bill-wave')
@section('header-title', 'Manajemen Transaksi')

@section('content')
<div class="container-fluid px-0">
    <!-- ====================== STATISTIK ====================== -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stats-number">{{ $statistics['total'] ?? 0 }}</div>
                <div class="stats-label">Total Transaksi</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ $statistics['total_success'] ?? 0 }}</div>
                <div class="stats-label">Sukses</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ $statistics['total_pending'] ?? 0 }}</div>
                <div class="stats-label">Menunggu</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-money-bill"></i>
                </div>
                <div class="stats-number">{{ $statistics['total_payment'] ?? 'Rp 0' }}</div>
                <div class="stats-label">Total Pembayaran</div>
            </div>
        </div>
    </div>

    <!-- ====================== TABEL TRANSAKSI ====================== -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Daftar Transaksi
                </h5>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('admin.transactions.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Cari transaksi..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="{{ route('admin.transactions.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i> Buat Transaksi
                    </a>
                </div>
            </div>

            <!-- Filter -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <!-- Filter Status -->
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('status'), ['status' => ''])) }}" 
                               class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                                Semua Status
                            </a>
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}" 
                               class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                                Menunggu
                            </a>
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('status'), ['status' => 'success'])) }}" 
                               class="btn btn-outline-success {{ request('status') == 'success' ? 'active' : '' }}">
                                Sukses
                            </a>
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('status'), ['status' => 'failed'])) }}" 
                               class="btn btn-outline-danger {{ request('status') == 'failed' ? 'active' : '' }}">
                                Gagal
                            </a>
                        </div>
                        
                        <!-- Filter Jenis (Hanya Pembayaran) -->
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('jenis'), ['jenis' => ''])) }}" 
                               class="btn btn-outline-secondary {{ !request('jenis') ? 'active' : '' }}">
                                Semua Jenis
                            </a>
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('jenis'), ['jenis' => 'payment'])) }}" 
                               class="btn btn-outline-success {{ request('jenis') == 'payment' ? 'active' : '' }}">
                                Pembayaran
                            </a>
                            <a href="{{ route('admin.transactions.index', array_merge(request()->except('jenis'), ['jenis' => 'penalty'])) }}" 
                               class="btn btn-outline-danger {{ request('jenis') == 'penalty' ? 'active' : '' }}">
                                Denda
                            </a>
                        </div>
                        
                        <!-- Filter Tanggal -->
                        <form method="GET" action="{{ route('admin.transactions.index') }}" class="d-flex gap-2">
                            @foreach(request()->except(['start_date', 'end_date', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="date" name="start_date" class="form-control form-control-sm" 
                                   value="{{ request('start_date') }}" placeholder="Dari" style="width: 130px;">
                            <input type="date" name="end_date" class="form-control form-control-sm" 
                                   value="{{ request('end_date') }}" placeholder="Sampai" style="width: 130px;">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if(request('start_date') || request('end_date'))
                                <a href="{{ route('admin.transactions.index', request()->except(['start_date', 'end_date'])) }}" 
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        32
                            <th>Kode</th>
                            <th>User</th>
                            <th>Jenis</th>
                            <th>Jumlah Bayar</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $transaction->kode_transaksi ?? 'TRX-'.$transaction->id }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $transaction->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $transaction->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                @php
                                    $jenisColors = [
                                        'payment' => 'success',
                                        'penalty' => 'danger'
                                    ];
                                    $jenisText = [
                                        'payment' => 'Pembayaran',
                                        'penalty' => 'Denda'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $jenisColors[$transaction->jenis_transaksi] ?? 'secondary' }}">
                                    {{ $jenisText[$transaction->jenis_transaksi] ?? $transaction->jenis_transaksi }}
                                </span>
                                @if($transaction->peminjaman_id)
                                    <br>
                                    <small class="text-muted">Peminjaman #{{ $transaction->peminjaman_id }}</small>
                                @endif
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}
                                @if($transaction->metode_pembayaran)
                                    <br>
                                    <small class="text-muted">{{ $transaction->metode_pembayaran }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'success' => 'success',
                                        'failed' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $statusText = [
                                        'pending' => 'Menunggu',
                                        'success' => 'Sukses',
                                        'failed' => 'Gagal',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$transaction->status] ?? 'secondary' }}">
                                    {{ $statusText[$transaction->status] ?? $transaction->status }}
                                </span>
                                @if($transaction->confirmed_by)
                                    <br>
                                    <small class="text-muted">Oleh: {{ $transaction->confirmer->name ?? '-' }}</small>
                                @endif
                            </td>
                            <td>
                                @if($transaction->tanggal_transaksi)
                                    {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('H:i') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                       class="btn btn-outline-info" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($transaction->status === 'pending')
                                        @if($transaction->jenis_transaksi === 'payment' || $transaction->jenis_transaksi === 'penalty')
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="approveTransaction({{ $transaction->id }})" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="rejectTransaction({{ $transaction->id }})" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('admin.transactions.edit', $transaction) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-outline-secondary" 
                                                onclick="cancelTransaction({{ $transaction->id }})" title="Batal">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                    
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteTransaction({{ $transaction->id }}, '{{ $transaction->kode_transaksi ?? 'TRX-'.$transaction->id }}')" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada transaksi</p>
                                    <small>Klik "Buat Transaksi" untuk menambahkan data baru</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} 
                    dari {{ $transactions->total() }} transaksi
                </div>
                <div>
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Approve transaction
    function approveTransaction(id) {
        Swal.fire({
            title: 'Setujui Transaksi?',
            text: "Transaksi akan disetujui.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/transactions') }}/${id}/approve`;
                form.style.display = 'none';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // Reject transaction
    function rejectTransaction(id) {
        Swal.fire({
            title: 'Tolak Transaksi?',
            text: "Transaksi akan ditolak.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/transactions') }}/${id}/reject`;
                form.style.display = 'none';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // Cancel transaction
    function cancelTransaction(id) {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Transaksi akan dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/transactions') }}/${id}/cancel`;
                form.style.display = 'none';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // Delete transaction
    function deleteTransaction(id, kode) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Transaksi <strong>${kode}</strong> akan dihapus permanen!<br>
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

<style>
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        height: 100%;
        transition: transform 0.3s ease;
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
        margin-bottom: 20px;
    }
    
    .stats-number {
        font-size: 32px;
        font-weight: 700;
        margin: 10px 0;
        color: #2c3e50;
    }
    
    .stats-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .btn-group .btn.active {
        background-color: #3a86ff;
        color: white;
        border-color: #3a86ff;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .fw-bold {
        font-weight: 600 !important;
    }
</style>
@endpush