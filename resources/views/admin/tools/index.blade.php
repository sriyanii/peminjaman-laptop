@extends('layouts.app')

@section('title', 'Manajemen Alat')
@section('header-icon', 'fas fa-tools')
@section('header-title', 'Manajemen Alat')

@section('content')
<div class="container-fluid px-0">
    <!-- ====================== STATISTIK ====================== -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stats-number">{{ $statistics['total_tools'] ?? 0 }}</div>
                <div class="stats-label">Total Alat</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ $statistics['available_tools'] ?? 0 }}</div>
                <div class="stats-label">Tersedia</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="stats-number">{{ $statistics['borrowed_tools'] ?? 0 }}</div>
                <div class="stats-label">Dipinjam</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number">{{ $statistics['maintenance_tools'] ?? 0 }}</div>
                <div class="stats-label">Maintenance</div>
            </div>
        </div>
    </div>

    <!-- ====================== TABEL ALAT ====================== -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Daftar Alat/Laptop
                </h5>
                <div class="d-flex gap-2">
                    <!-- PERBAIKAN: route name dari admin.tools.index -> admin.laptops.index -->
                    <form method="GET" action="{{ route('admin.laptops.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Cari alat..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <!-- PERBAIKAN: route name dari admin.tools.create -> admin.laptops.create -->
                    <a href="{{ route('admin.laptops.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i> Tambah Alat
                    </a>
                </div>
            </div>

            <!-- Filter Status - PERBAIKAN semua route name -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.laptops.index') }}" 
                           class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.laptops.index', ['status' => 'tersedia']) }}" 
                           class="btn btn-outline-success {{ request('status') == 'tersedia' ? 'active' : '' }}">
                            Tersedia
                        </a>
                        <a href="{{ route('admin.laptops.index', ['status' => 'dipinjam']) }}" 
                           class="btn btn-outline-warning {{ request('status') == 'dipinjam' ? 'active' : '' }}">
                            Dipinjam
                        </a>
                        <a href="{{ route('admin.laptops.index', ['status' => 'rusak']) }}" 
                           class="btn btn-outline-danger {{ request('status') == 'rusak' ? 'active' : '' }}">
                            Rusak
                        </a>
                        <a href="{{ route('admin.laptops.index', ['status' => 'maintenance']) }}" 
                           class="btn btn-outline-info {{ request('status') == 'maintenance' ? 'active' : '' }}">
                            Maintenance
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="80">Foto</th>
                            <th>Kode</th>
                            <th>Nama Alat</th>
                            <th>Merk/Type</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Kondisi</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laptops as $laptop)
                        <tr>
                            <!-- ================ KOLOM FOTO ================ -->
                            <td>
                                <!-- PERBAIKAN: Sesuaikan dengan field gambar di database -->
                                <div class="tool-photo" onclick="showPhotoModal('{{ $laptop->gambar ? asset($laptop->gambar) : asset('assets/img/no-image.png') }}', '{{ $laptop->merk }} {{ $laptop->model }}')">
                                    @if(!empty($laptop->gambar))
                                        <img src="{{ asset($laptop->gambar) }}" 
                                             alt="{{ $laptop->merk }} {{ $laptop->model }}"
                                             class="img-thumbnail rounded"
                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/img/no-image.png') }}';">
                                    @else
                                        <div class="no-photo bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 60px; cursor: pointer;">
                                            <i class="fas fa-camera text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- PERBAIKAN: Kode alat dari ID -->
                            <td>
                                <span class="badge bg-light text-dark">#LAP-{{ str_pad($laptop->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            
                            <!-- PERBAIKAN: Nama alat dari merk + model -->
                            <td>
                                <div class="fw-bold">{{ $laptop->merk }} {{ $laptop->model }}</div>
                                <small class="text-muted">SN: {{ $laptop->serial_number ?? '-' }}</small>
                            </td>
                            
                            <!-- Merk/Model -->
                            <td>
                                <div>{{ $laptop->merk }}</div>
                                <small class="text-muted">{{ $laptop->model }}</small>
                            </td>
                            
                            <!-- Kategori - sementara nonaktifkan jika tidak ada relasi -->
                            <td>
                                <span class="badge bg-secondary">-</span>
                            </td>
                            
                            <!-- Status -->
                            <td>
                                @php
                                    $statusColors = [
                                        'tersedia' => 'success',
                                        'dipinjam' => 'warning',
                                        'rusak' => 'danger',
                                        'maintenance' => 'info'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$laptop->status] ?? 'secondary' }}">
                                    {{ ucfirst($laptop->status) }}
                                </span>
                            </td>
                            
                            <!-- Kondisi -->
                            <td>
                                @php
                                    $conditionColors = [
                                        'baik' => 'success',
                                        'rusak_ringan' => 'warning',
                                        'rusak_berat' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $conditionColors[$laptop->kondisi] ?? 'secondary' }}">
                                    {{ str_replace('_', ' ', ucfirst($laptop->kondisi)) }}
                                </span>
                            </td>
                            
                            <!-- Lokasi -->
                            <td>{{ $laptop->lokasi ?? '-' }}</td>
                            
                            <!-- Aksi - PERBAIKAN semua route name -->
                            <td class="text-nowrap">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.laptops.show', $laptop->id) }}"
                                       class="btn btn-outline-info btn-sm rounded-circle action-btn"
                                       title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.laptops.edit', $laptop->id) }}"
                                       class="btn btn-outline-primary btn-sm rounded-circle action-btn"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm rounded-circle action-btn"
                                            title="Hapus"
                                            onclick="confirmDelete({{ $laptop->id }}, '{{ $laptop->merk }} {{ $laptop->model }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $laptop->id }}" 
                                          action="{{ route('admin.laptops.destroy', $laptop->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-tools fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada alat</p>
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
                    Menampilkan {{ $laptops->firstItem() ?? 0 }} - {{ $laptops->lastItem() ?? 0 }} dari {{ $laptops->total() }} alat
                </div>
                <div>
                    {{ $laptops->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================ MODAL PREVIEW FOTO ================ -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalTitle">Foto Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="modalPhoto" src="" alt="Foto Alat" class="img-fluid" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tooltip
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Show photo modal
    function showPhotoModal(imageUrl, title) {
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        document.getElementById('modalPhoto').src = imageUrl;
        document.getElementById('photoModalTitle').textContent = 'Foto: ' + title;
        modal.show();
    }
    
    // Confirm delete
    function confirmDelete(toolId, toolName) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Alat <strong>${toolName}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${toolId}`).submit();
            }
        });
    }
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
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .action-btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
    }
    
    .tool-photo {
        transition: transform 0.2s;
    }
    
    .tool-photo:hover {
        transform: scale(1.05);
    }
    
    .no-photo {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
@endsection