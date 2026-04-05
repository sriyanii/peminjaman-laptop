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
                    <!-- HAPUS TOMBOL BUAT PEMINJAMAN - Admin tidak bisa menambah -->
                </div>
            </div>

            <!-- Filter Status - DISELARASKAN DENGAN PETUGAS -->
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

            <!-- Filter Tambahan - DISELARASKAN DENGAN PETUGAS -->
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.borrowings.index') }}" class="row g-3">
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
                <table class="table table-hover">
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
                            <th>Aksi</th>
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
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" 
                                       class="btn btn-outline-info" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($borrowing->status === 'pending')
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="approveBorrowing({{ $borrowing->id }})" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="rejectBorrowing({{ $borrowing->id }})" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                   @if(in_array($borrowing->status, ['approved', 'aktif']) && !$borrowing->tanggal_kembali)
    <button type="button" class="btn btn-outline-primary" 
            onclick="returnTool({{ $borrowing->id }})" title="Kembalikan">
        <i class="fas fa-undo"></i>
    </button>
@endif
                                    
                                    @if(in_array($borrowing->status, ['pending', 'ditolak']))
                                        <a href="{{ route('admin.borrowings.edit', $borrowing->id) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteBorrowing({{ $borrowing->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Modal Setujui -->
<div class="modal fade" id="setujuiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui peminjaman <strong id="setujuiKode"></strong>?</p>
                <div class="mb-3">
                    <label for="catatanSetujui" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control" id="catatanSetujui" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="setujuiForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="approved">
                    <input type="hidden" name="catatan" id="catatanSetujuiInput">
                    <button type="submit" class="btn btn-success">Setujui</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="tolakModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menolak peminjaman <strong id="tolakKode"></strong>?</p>
                <div class="mb-3">
                    <label for="alasanTolak" class="form-label">Alasan Penolakan *</label>
                    <textarea class="form-control" id="alasanTolak" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="tolakForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="ditolak">
                    <input type="hidden" name="alasan_ditolak" id="alasanTolakInput">
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Reset forms when modal is hidden
        $('.modal').on('hidden.bs.modal', function () {
            $(this).find('form')[0]?.reset();
            $(this).find('button[type="submit"]').prop('disabled', false);
            $(this).find('button[type="submit"]').html($(this).attr('id') === 'setujuiModal' ? 'Setujui' : 'Tolak');
        });
    });
    
    // Approve peminjaman dengan modal
    function approveBorrowing(id) {
        $.get('/admin/borrowings/' + id + '/get-kode', function(data) {
            $('#setujuiKode').text(data.kode);
            $('#setujuiForm').attr('action', '/admin/borrowings/' + id + '/update-status');
            const setujuiModal = new bootstrap.Modal(document.getElementById('setujuiModal'));
            setujuiModal.show();
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat data peminjaman'
            });
        });
    }
    
    // Reject peminjaman dengan modal
    function rejectBorrowing(id) {
        $.get('/admin/borrowings/' + id + '/get-kode', function(data) {
            $('#tolakKode').text(data.kode);
            $('#tolakForm').attr('action', '/admin/borrowings/' + id + '/update-status');
            const tolakModal = new bootstrap.Modal(document.getElementById('tolakModal'));
            tolakModal.show();
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat data peminjaman'
            });
        });
    }
    
    // Delete peminjaman
    function deleteBorrowing(id) {
        Swal.fire({
            title: 'Hapus Peminjaman?',
            text: "Data peminjaman akan dihapus permanen!",
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
                form.action = `/admin/borrowings/${id}`;
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
    
    // Handle form submissions for modals
    $('#setujuiForm').submit(function(e) {
        const catatan = $('#catatanSetujui').val();
        $('#catatanSetujuiInput').val(catatan);
        
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        $(this).find('button[type="submit"]').prop('disabled', true);
    });

    $('#tolakForm').submit(function(e) {
        const alasan = $('#alasanTolak').val();
        
        if (!alasan.trim()) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Mohon berikan alasan penolakan!'
            });
            return false;
        }
        
        $('#alasanTolakInput').val(alasan);
        
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
    
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
        color: var(--dark);
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
    
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        color: #3a86ff;
        border-radius: 4px;
        margin: 0 2px;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #3a86ff;
        border-color: #3a86ff;
        color: white;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        color: #2667cc;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
    }
    
    @media (max-width: 768px) {
        .pagination-container {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 5px;
        }
        
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .col-md-3 {
            margin-bottom: 1rem;
        }
        
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .table td {
            min-width: 120px;
        }
    }
    
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #eef2f7;
    }
    
    .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        padding: 1rem 1.25rem;
    }
    
    .card-header .card-title {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.02);
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 6px;
    }
    
    .btn-group-sm .btn + .btn {
        margin-left: 2px;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
    }
    
    .opacity-50 {
        opacity: 0.5;
    }
    
    .page-link {
        border-radius: 6px !important;
        margin: 0 2px;
        padding: 0.375rem 0.75rem;
        color: #2c3e50;
        border: 1px solid #dee2e6;
    }
    
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>
@endsection