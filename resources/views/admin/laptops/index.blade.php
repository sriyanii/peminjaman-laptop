@extends('layouts.app')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
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

    <!-- Main content -->
    <div class="row mb-4 g-3">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-laptop me-2"></i>Daftar Alat
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="text-muted">
                        Total: {{ $laptops->total() }} alat
                    </span>
                </div>
            </div>

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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter Status -->
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
                    <a href="{{ route('admin.laptops.create') }}" class="btn btn-sm btn-success ms-2">
                        <i class="fas fa-plus me-1"></i> Tambah Alat
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.laptops.index') }}" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari berdasarkan merk, model, atau serial number..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alat Cards -->
            <div class="row">
                @forelse($laptops as $laptop)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    @if($laptop->status == 'tersedia')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Tersedia
                                        </span>
                                    @elseif($laptop->status == 'dipinjam')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-user-clock me-1"></i>Dipinjam
                                        </span>
                                    @elseif($laptop->status == 'maintenance')
                                        <span class="badge bg-info">
                                            <i class="fas fa-tools me-1"></i>Maintenance
                                        </span>
                                    @elseif($laptop->status == 'rusak')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Rusak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $laptop->status }}</span>
                                    @endif
                                </h6>
                                <span class="badge bg-secondary">#LAP-{{ str_pad($laptop->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($laptop->gambar)
                                    <img src="{{ asset($laptop->gambar) }}" 
                                         alt="{{ $laptop->merk }} {{ $laptop->model }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 150px; object-fit: cover; cursor: pointer;"
                                         onclick="showPhotoModal('{{ asset($laptop->gambar) }}', '{{ $laptop->merk }} {{ $laptop->model }}')">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 150px; cursor: pointer;"
                                         onclick="showPhotoModal('{{ asset('assets/img/no-image.png') }}', '{{ $laptop->merk }} {{ $laptop->model }}')">
                                        <i class="fas fa-laptop fa-3x text-secondary"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h5 class="card-title">{{ $laptop->merk }} {{ $laptop->model }}</h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-microchip me-2"></i>{{ $laptop->processor ?? '-' }} | {{ $laptop->ram ?? '-' }} RAM<br>
                                @if($laptop->storage)
                                <i class="fas fa-database me-2"></i>{{ $laptop->storage }}<br>
                                @endif
                                <i class="fas fa-barcode me-2"></i>SN: {{ $laptop->serial_number }}
                            </p>
                            
                            <div class="mb-3">
                                @if($laptop->tahun_pembelian)
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Tahun: {{ $laptop->tahun_pembelian }}
                                </small><br>
                                @endif
                                
                                @if($laptop->lokasi)
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Lokasi: {{ $laptop->lokasi }}
                                </small>
                                @endif
                            </div>
                            
                            @if($laptop->kondisi)
                            <div class="mb-3">
                                @php
                                    $conditionBadge = [
                                        'baik' => 'success',
                                        'sedang' => 'warning',
                                        'rusak_ringan' => 'warning',
                                        'rusak_berat' => 'danger'
                                    ];
                                    $badgeColor = $conditionBadge[$laptop->kondisi] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">
                                    <i class="fas fa-{{ $laptop->kondisi == 'baik' ? 'check-circle' : ($laptop->kondisi == 'rusak_berat' ? 'times-circle' : 'exclamation-circle') }} me-1"></i>
                                    Kondisi: {{ ucfirst(str_replace('_', ' ', $laptop->kondisi)) }}
                                </span>
                            </div>
                            @endif
                            
                            @if($laptop->harga_sewa_harian)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Harga Sewa: Rp {{ number_format($laptop->harga_sewa_harian, 0, ',', '.') }}
                                </small>
                            </div>
                            @endif

                            <!-- Tombol Detail -->
                            <button type="button" 
                                    class="btn btn-outline-info btn-sm w-100 mb-2"
                                    onclick="loadDetail({{ $laptop->id }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </button>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.laptops.edit', $laptop->id) }}"
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm w-100"
                                        onclick="confirmDelete({{ $laptop->id }}, '{{ addslashes($laptop->merk . ' ' . $laptop->model) }}')">
                                    <i class="fas fa-trash me-2"></i>Hapus
                                </button>
                            </div>
                            <form id="delete-form-{{ $laptop->id }}" 
                                  action="{{ route('admin.laptops.destroy', $laptop->id) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada data alat/laptop.
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($laptops->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $laptops->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ================ MODAL PREVIEW FOTO ================ -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalTitle">
                    <i class="fas fa-image me-2"></i>Foto Alat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="modalPhoto" src="" alt="Foto Alat" class="img-fluid" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- ================ MODAL DETAIL ALAT (AJAX) ================ -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-tools text-primary me-2"></i>Detail Alat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show photo modal
    function showPhotoModal(imageUrl, title) {
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        document.getElementById('modalPhoto').src = imageUrl;
        document.getElementById('photoModalTitle').innerHTML = '<i class="fas fa-image me-2"></i>Foto: ' + title;
        modal.show();
    }
    
    // Load detail via AJAX
    function loadDetail(id) {
        document.getElementById('detailModalBody').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;
        
        fetch(`/admin/laptops/${id}/detail`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data');
                }
                return response.json();
            })
            .then(laptop => {
                const formatRupiah = (value) => {
                    if (!value) return '-';
                    return 'Rp ' + parseFloat(value).toLocaleString('id-ID');
                };
                
                const formatDate = (date) => {
                    if (!date) return '-';
                    const d = new Date(date);
                    return d.toLocaleDateString('id-ID');
                };
                
                let html = `
                    <div class="row">
                        <div class="col-md-12 text-center mb-3">
                            ${laptop.gambar ? 
                                `<img src="/${laptop.gambar}" alt="${laptop.merk} ${laptop.model}" style="max-height: 150px; object-fit: cover; cursor: pointer;" class="img-thumbnail" onclick="showPhotoModal('/${laptop.gambar}', '${laptop.merk} ${laptop.model}')">` : 
                                `<div class="bg-light p-3 d-inline-block rounded"><i class="fas fa-laptop text-secondary fa-3x"></i></div>`
                            }
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-row">
                                <div class="detail-label">Kode Alat</div>
                                <div class="detail-value"><span class="badge bg-primary">#LAP-${String(laptop.id).padStart(5, '0')}</span></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Merk</div>
                                <div class="detail-value">${laptop.merk || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Model</div>
                                <div class="detail-value">${laptop.model || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Processor</div>
                                <div class="detail-value">${laptop.processor || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">RAM</div>
                                <div class="detail-value">${laptop.ram || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Storage</div>
                                <div class="detail-value">${laptop.storage || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Serial Number</div>
                                <div class="detail-value">${laptop.serial_number || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Tahun Pembelian</div>
                                <div class="detail-value">${laptop.tahun_pembelian || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Lokasi</div>
                                <div class="detail-value">${laptop.lokasi || '-'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-row">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="badge bg-${laptop.status === 'tersedia' ? 'success' : (laptop.status === 'dipinjam' ? 'warning' : (laptop.status === 'rusak' ? 'danger' : 'info'))}">
                                        ${laptop.status === 'tersedia' ? 'Tersedia' : (laptop.status === 'dipinjam' ? 'Dipinjam' : (laptop.status === 'rusak' ? 'Rusak' : 'Maintenance'))}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Kondisi</div>
                                <div class="detail-value">
                                    <span class="badge bg-${laptop.kondisi === 'baik' ? 'success' : (laptop.kondisi === 'rusak_ringan' ? 'warning' : 'danger')}">
                                        ${laptop.kondisi ? laptop.kondisi.replace('_', ' ') : '-'}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Warna</div>
                                <div class="detail-value">${laptop.warna || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">OS</div>
                                <div class="detail-value">${laptop.os || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Baterai Kondisi</div>
                                <div class="detail-value">${laptop.baterai_kondisi || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Garansi</div>
                                <div class="detail-value">${laptop.garansi || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Garansi Berakhir</div>
                                <div class="detail-value">${formatDate(laptop.garansi_berakhir)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Harga Beli</div>
                                <div class="detail-value">${formatRupiah(laptop.harga_beli)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Harga Sewa</div>
                                <div class="detail-value">${formatRupiah(laptop.harga_sewa_harian)}</div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="detail-row">
                                <div class="detail-label">Keterangan</div>
                                <div class="detail-value">${laptop.keterangan || '-'}</div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('detailModalBody').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('detailModalBody').innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                        <p class="mb-0">Gagal memuat data</p>
                        <small>${error.message}</small>
                    </div>
                `;
            });
    }
    
    // Confirm delete with SweetAlert2
    function confirmDelete(toolId, toolName) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Alat <strong>${toolName}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form delete
                const form = document.getElementById(`delete-form-${toolId}`);
                if (form) {
                    form.submit();
                } else {
                    Swal.fire('Error!', 'Form tidak ditemukan', 'error');
                }
            }
        });
    }

    $(document).ready(function() {
        // Auto dismiss alert after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').fadeTo(500, 0).slideUp(500, function(){
                $(this).alert('close');
            });
        }, 5000);
    });
</script>


@endpush