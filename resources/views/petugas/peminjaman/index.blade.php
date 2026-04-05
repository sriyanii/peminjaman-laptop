@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-md-12 ms-sm-auto px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-laptop me-2"></i>Manajemen Peminjaman
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('petugas.peminjaman.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Peminjaman
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total</h6>
                                    <h2 class="mb-0">{{ $totalPeminjaman ?? 0 }}</h2>
                                </div>
                                <i class="fas fa-laptop fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Disetujui</h6>
                                    <h2 class="mb-0">{{ $peminjamanDisetujui ?? 0 }}</h2>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Menunggu</h6>
                                    <h2 class="mb-0">{{ $peminjamanMenunggu ?? 0 }}</h2>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Ditolak</h6>
                                    <h2 class="mb-0">{{ $peminjamanDitolak ?? 0 }}</h2>
                                </div>
                                <i class="fas fa-times-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                   <form method="GET" action="{{ route('petugas.peminjaman.index') }}" class="row g-3">
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
                            <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">
    <i class="fas fa-redo me-2"></i>Reset
</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Peminjam</th>
                                    <th>Laptop</th>
                                    <th>Tgl. Pinjam</th>
                                    <th>Tgl. Kmbl</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($peminjaman as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $item->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($item->created_at)) . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $item->user->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $alat = $item->laptop ?? $item->laptop;
                                        @endphp
                                        
                                        @if($alat)
                                            <div class="fw-bold">{{ $alat->nama_alat ?? 'Alat' }}</div>
                                            <small class="text-muted">
                                                {{ $alat->merk ?? '' }} {{ $alat->type ?? ($alat->model ?? '') }}
                                                <br>Kode: {{ $alat->kode_alat ?? '-' }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tanggal_pinjam)
                                            {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tanggal_kembali_rencana)
                                            {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}
                                        @elseif($item->tanggal_kembali)
                                            {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                            <br>
                                            <small class="text-success">(Dikembalikan)</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['color' => 'warning', 'text' => 'Menunggu'],
                                                'approved' => ['color' => 'success', 'text' => 'Disetujui'],
                                                'aktif' => ['color' => 'primary', 'text' => 'Aktif'],
                                                'selesai' => ['color' => 'info', 'text' => 'Selesai'],
                                                'ditolak' => ['color' => 'danger', 'text' => 'Ditolak'],
                                                'batal' => ['color' => 'secondary', 'text' => 'Batal'],
                                                'terlambat' => ['color' => 'danger', 'text' => 'Terlambat']
                                            ];
                                            
                                            $statusInfo = $statusConfig[$item->status] ?? ['color' => 'secondary', 'text' => ucfirst($item->status)];
                                            
                                            $isTerlambat = in_array($item->status, ['approved', 'aktif']) && 
                                                           $item->tanggal_kembali_rencana && 
                                                           \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast() &&
                                                           !$item->tanggal_kembali;
                                        @endphp
                                        
                                        <span class="badge bg-{{ $statusInfo['color'] }}">
                                            {{ $statusInfo['text'] }}
                                        </span>
                                        
                                        @if($isTerlambat)
                                            <br>
                                            <span class="badge bg-danger mt-1">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                            </span>
                                        @endif
                                        
                                        @if($item->denda > 0 && !$item->is_denda_dibayar)
                                            <br>
                                            <span class="badge bg-warning mt-1">
                                                <i class="fas fa-money-bill-wave me-1"></i>Denda: Rp {{ number_format($item->denda, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Detail Button -->
                                            <a href="{{ route('petugas.peminjaman.index', $item->id) }}" 
                                               class="btn btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Setujui Button (only for pending status) -->
                                            @if($item->status == 'pending')
                                                <button type="button" class="btn btn-success setuju-btn" 
                                                        title="Setujui"
                                                        data-id="{{ $item->id }}"
                                                        data-kode="{{ $item->kode_peminjaman ?? $item->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                
                                                <!-- Tolak Button -->
                                                <button type="button" class="btn btn-danger tolak-btn" 
                                                        title="Tolak"
                                                        data-id="{{ $item->id }}"
                                                        data-kode="{{ $item->kode_peminjaman ?? $item->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            
                                            <!-- Pengembalian Button (for approved/aktif status) -->
                                            @if(in_array($item->status, ['approved', 'aktif']) && !$item->tanggal_kembali)
                                                <a href="{{ route('petugas.pengembalian.index') }}?peminjaman_id={{ $item->id }}" 
                                                   class="btn btn-primary" title="Proses Pengembalian">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- Delete Button -->
                                            <form action="{{ route('petugas.peminjaman.destroy', $item->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus peminjaman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">Tidak ada data peminjaman</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($peminjaman->hasPages())
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4">
                        <div class="text-muted small mb-2 mb-sm-0">
                            Menampilkan {{ $peminjaman->firstItem() ?? 0 }} - {{ $peminjaman->lastItem() ?? 0 }} 
                            dari {{ $peminjaman->total() }} data
                        </div>
                        <div>
                            {{ $peminjaman->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modals -->
<!-- Setujui Modal -->
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

<!-- Tolak Modal -->
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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* Custom Styles */
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
    
    .badge {
        font-weight: 500;
        padding: 5px 10px;
    }
    
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
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
    
    /* Pagination styling */
    .pagination {
        gap: 3px;
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
    
    /* Responsive */
    @media (max-width: 768px) {
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
</style>

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

        // Setujui confirmation
        $('.setuju-btn').click(function(e) {
            e.preventDefault();
            const peminjamanId = $(this).data('id');
            const kodePeminjaman = $(this).data('kode');
            
            $('#setujuiKode').text(kodePeminjaman);
            $('#setujuiForm').attr('action', '/petugas/peminjaman/' + peminjamanId + '/update-status');
            
            const setujuiModal = new bootstrap.Modal(document.getElementById('setujuiModal'));
            setujuiModal.show();
        });

        // Tolak confirmation
        $('.tolak-btn').click(function(e) {
            e.preventDefault();
            const peminjamanId = $(this).data('id');
            const kodePeminjaman = $(this).data('kode');
            
            $('#tolakKode').text(kodePeminjaman);
            $('#tolakForm').attr('action', '/petugas/peminjaman/' + peminjamanId + '/update-status');
            
            const tolakModal = new bootstrap.Modal(document.getElementById('tolakModal'));
            tolakModal.show();
        });

        // Handle form submissions
        $('#setujuiForm').submit(function(e) {
            const catatan = $('#catatanSetujui').val();
            $('#catatanSetujuiInput').val(catatan);
            
            // Show loading
            $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
            $(this).find('button[type="submit"]').prop('disabled', true);
        });

        $('#tolakForm').submit(function(e) {
            const alasan = $('#alasanTolak').val();
            
            if (!alasan.trim()) {
                e.preventDefault();
                alert('Mohon berikan alasan penolakan!');
                return false;
            }
            
            $('#alasanTolakInput').val(alasan);
            
            // Show loading
            $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
            $(this).find('button[type="submit"]').prop('disabled', true);
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
</script>
@endsection