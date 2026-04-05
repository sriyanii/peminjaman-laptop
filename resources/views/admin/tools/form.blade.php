@extends('layouts.app')

@section('title', $mode == 'show' ? 'Detail Alat: ' . $laptop->nama_alat : ($mode == 'edit' ? 'Edit Alat: ' . $laptop->nama_alat : 'Tambah Alat Baru'))
@section('header-icon', $mode == 'show' ? 'fas fa-laptops' : ($mode == 'edit' ? 'fas fa-edit' : 'fas fa-plus'))
@section('header-title', $mode == 'show' ? 'Detail Alat: ' . $laptop->nama_alat : ($mode == 'edit' ? 'Edit Alat' : 'Tambah Alat Baru'))

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="{{ $mode == 'show' ? 'col-lg-8' : 'col-lg-8' }}">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="{{ $mode == 'show' ? 'fas fa-laptops' : ($mode == 'edit' ? 'fas fa-edit text-primary' : 'fas fa-plus text-primary') }} me-2"></i>
                        @if($mode == 'show')
                            Detail Alat: {{ $laptop->nama_alat }}
                        @elseif($mode == 'edit')
                            Form Edit Alat: {{ $laptop->nama_alat }}
                        @else
                            Form Tambah Alat Baru
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    
                    @if($mode == 'show')
                        <!-- ================ SHOW MODE ================ -->
                        <div class="row">
                            <!-- ================ FOTO ALAT ================ -->
                            <div class="col-12 text-center mb-4">
                                @if(!empty($laptop->foto))
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset('foto_upload/' . $laptop->foto) }}" 
                                             alt="{{ $laptop->nama_alat }}"
                                             class="img-thumbnail shadow"
                                             style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/img/no-image.png') }}';">
                                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 border border-white" 
                                              data-bs-toggle="tooltip" 
                                              title="Foto Tersedia">
                                            <i class="fas fa-camera text-white fa-xs"></i>
                                        </span>
                                    </div>
                                @else
                                    <div class="no-photo d-inline-flex flex-column align-items-center justify-content-center bg-light rounded p-4"
                                         style="width: 200px; height: 200px;">
                                        <i class="fas fa-camera text-muted fa-4x mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada foto</p>
                                    </div>
                                @endif
                            </div>
                            <!-- ================ END FOTO ================ -->
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kode Alat</label>
                                <div class="form-control-plaintext fw-bold">#{{ $laptop->kode_alat }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nama Alat</label>
                                <div class="form-control-plaintext fw-bold">{{ $laptop->nama_alat }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Merk</label>
                                <div class="form-control-plaintext">{{ $laptop->merk }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Type/Model</label>
                                <div class="form-control-plaintext">{{ $laptop->type }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Serial Number</label>
                                <div class="form-control-plaintext">{{ $laptop->serial_number ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kategori</label>
                                <div class="form-control-plaintext">
                                    @if($laptop->category)
                                        <span class="badge bg-info">{{ $laptop->category->nama_kategori }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Status</label>
                                <div class="form-control-plaintext">
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
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kondisi</label>
                                <div class="form-control-plaintext">
                                    @php
                                        $conditionColors = [
                                            'baik' => 'success',
                                            'sedang' => 'warning',
                                            'buruk' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $conditionColors[$laptop->kondisi] ?? 'secondary' }}">
                                        {{ ucfirst($laptop->kondisi) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Lokasi</label>
                                <div class="form-control-plaintext">{{ $laptop->lokasi ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Pembelian</label>
                                <div class="form-control-plaintext">{{ $laptop->tanggal_pembelian ? \Carbon\Carbon::parse($laptop->tanggal_pembelian)->format('d/m/Y') : '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Harga</label>
                                <div class="form-control-plaintext">Rp {{ number_format($laptop->harga ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Masa Garansi</label>
                                <div class="form-control-plaintext">{{ $laptop->masa_garansi ?? '-' }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Spesifikasi</label>
                                <div class="form-control-plaintext" style="min-height: 100px;">{{ $laptop->spesifikasi ?? '-' }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Keterangan</label>
                                <div class="form-control-plaintext">{{ $laptop->keterangan ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Ditambahkan</label>
                                <div class="form-control-plaintext">{{ $laptop->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Terakhir Update</label>
                                <div class="form-control-plaintext">{{ $laptop->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                    @else
                        <!-- ================ CREATE/EDIT MODE ================ -->
                        <form method="POST" 
                              action="{{ $mode == 'edit' ? route('admin.laptops.update', $laptop->id) : route('admin.laptops.store') }}" 
                              id="toolForm" 
                              enctype="multipart/form-data">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif
                            
                            <div class="row">
                                <!-- Kolom Kiri: Form Data -->
                                <div class="col-md-7">
                                    <!-- Kode Alat -->
                                    <div class="mb-3">
                                        <label for="kode_alat" class="form-label">Kode Alat <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('kode_alat') is-invalid @enderror" 
                                               id="kode_alat" 
                                               name="kode_alat" 
                                               value="{{ old('kode_alat', $laptop->kode_alat ?? '') }}"
                                               required
                                               placeholder="Contoh: LP001">
                                        @error('kode_alat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Nama Alat -->
                                    <div class="mb-3">
                                        <label for="nama_alat" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('nama_alat') is-invalid @enderror" 
                                               id="nama_alat" 
                                               name="nama_alat" 
                                               value="{{ old('nama_alat', $laptop->nama_alat ?? '') }}"
                                               required
                                               placeholder="Contoh: Laptop Dell XPS 15">
                                        @error('nama_alat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Merk -->
                                    <div class="mb-3">
                                        <label for="merk" class="form-label">Merk <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('merk') is-invalid @enderror" 
                                               id="merk" 
                                               name="merk" 
                                               value="{{ old('merk', $laptop->merk ?? '') }}"
                                               required
                                               placeholder="Contoh: Dell, HP, Lenovo">
                                        @error('merk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Type -->
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type/Model <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('type') is-invalid @enderror" 
                                               id="type" 
                                               name="type" 
                                               value="{{ old('type', $laptop->type ?? '') }}"
                                               required
                                               placeholder="Contoh: XPS 15, ThinkPad T14">
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Serial Number -->
                                    <div class="mb-3">
                                        <label for="serial_number" class="form-label">Serial Number</label>
                                        <input type="text" 
                                               class="form-control @error('serial_number') is-invalid @enderror" 
                                               id="serial_number" 
                                               name="serial_number" 
                                               value="{{ old('serial_number', $laptop->serial_number ?? '') }}"
                                               placeholder="Nomor seri alat">
                                        @error('serial_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Kategori -->
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-control @error('category_id') is-invalid @enderror" 
                                                id="category_id" 
                                                name="category_id" 
                                                required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $laptop->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required>
                                            <option value="">Pilih Status</option>
                                            <option value="tersedia" {{ old('status', $laptop->status ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                            <option value="dipinjam" {{ old('status', $laptop->status ?? '') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                            <option value="rusak" {{ old('status', $laptop->status ?? '') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                            <option value="maintenance" {{ old('status', $laptop->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Kondisi -->
                                    <div class="mb-3">
                                        <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                        <select class="form-control @error('kondisi') is-invalid @enderror" 
                                                id="kondisi" 
                                                name="kondisi" 
                                                required>
                                            <option value="">Pilih Kondisi</option>
                                            <option value="baik" {{ old('kondisi', $laptop->kondisi ?? '') == 'baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="sedang" {{ old('kondisi', $laptop->kondisi ?? '') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                            <option value="buruk" {{ old('kondisi', $laptop->kondisi ?? '') == 'buruk' ? 'selected' : '' }}>Buruk</option>
                                        </select>
                                        @error('kondisi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Lokasi -->
                                    <div class="mb-3">
                                        <label for="lokasi" class="form-label">Lokasi</label>
                                        <input type="text" 
                                               class="form-control @error('lokasi') is-invalid @enderror" 
                                               id="lokasi" 
                                               name="lokasi" 
                                               value="{{ old('lokasi', $laptop->lokasi ?? '') }}"
                                               placeholder="Contoh: Rak A1, Ruang Server">
                                        @error('lokasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Tanggal Pembelian -->
                                    <div class="mb-3">
                                        <label for="tanggal_pembelian" class="form-label">Tanggal Pembelian</label>
                                        <input type="date" 
                                               class="form-control @error('tanggal_pembelian') is-invalid @enderror" 
                                               id="tanggal_pembelian" 
                                               name="tanggal_pembelian" 
                                               value="{{ old('tanggal_pembelian', $laptop->tanggal_pembelian ?? '') }}">
                                        @error('tanggal_pembelian')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Harga -->
                                    <div class="mb-3">
                                        <label for="harga" class="form-label">Harga</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('harga') is-invalid @enderror" 
                                                   id="harga" 
                                                   name="harga" 
                                                   value="{{ old('harga', $laptop->harga ?? '') }}"
                                                   placeholder="0">
                                        </div>
                                        @error('harga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Masa Garansi -->
                                    <div class="mb-3">
                                        <label for="masa_garansi" class="form-label">Masa Garansi</label>
                                        <input type="text" 
                                               class="form-control @error('masa_garansi') is-invalid @enderror" 
                                               id="masa_garansi" 
                                               name="masa_garansi" 
                                               value="{{ old('masa_garansi', $laptop->masa_garansi ?? '') }}"
                                               placeholder="Contoh: 1 Tahun, 2025-12-31">
                                        @error('masa_garansi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Spesifikasi -->
                                    <div class="mb-3">
                                        <label for="spesifikasi" class="form-label">Spesifikasi</label>
                                        <textarea class="form-control @error('spesifikasi') is-invalid @enderror" 
                                                  id="spesifikasi" 
                                                  name="spesifikasi" 
                                                  rows="3"
                                                  placeholder="Contoh: Intel i7, 16GB RAM, 512GB SSD">{{ old('spesifikasi', $laptop->spesifikasi ?? '') }}</textarea>
                                        @error('spesifikasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Keterangan -->
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                                  id="keterangan" 
                                                  name="keterangan" 
                                                  rows="2"
                                                  placeholder="Catatan tambahan">{{ old('keterangan', $laptop->keterangan ?? '') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- ================ KOLOM KANAN: UPLOAD FOTO ================ -->
                                <div class="col-md-5">
                                    <div class="card border-0 bg-light mb-4">
                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-3">
                                                <i class="fas fa-camera me-2"></i>Foto Alat
                                            </h6>
                                            
                                            <!-- Preview Foto Saat Ini -->
                                            <div id="currentPhotoContainer" class="mb-3">
                                                @if(isset($laptop) && !empty($laptop->foto))
                                                    <div class="position-relative d-inline-block">
                                                        <img src="{{ asset('foto_upload/' . $laptop->foto) }}" 
                                                             alt="{{ $laptop->nama_alat }}"
                                                             class="img-thumbnail border"
                                                             style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                                             onerror="this.onerror=null; this.src='{{ asset('assets/img/no-image.png') }}';">
                                                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1 border border-white">
                                                            <i class="fas fa-check text-white fa-xs"></i>
                                                        </span>
                                                    </div>
                                                    <p class="text-muted small mt-2 mb-0">Foto saat ini</p>
                                                @else
                                                    <div class="no-photo d-inline-flex flex-column align-items-center justify-content-center bg-white rounded p-4"
                                                         style="width: 200px; height: 200px;">
                                                        <i class="fas fa-camera text-muted fa-4x mb-2"></i>
                                                        <p class="text-muted mb-0">Belum ada foto</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Preview Foto Baru -->
                                            <div id="photoPreviewContainer" class="mb-3" style="display: none;">
                                                <div class="position-relative d-inline-block">
                                                    <img id="photoPreview" 
                                                         src="#" 
                                                         alt="Preview"
                                                         class="img-thumbnail border border-primary"
                                                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle"
                                                            id="removePhotoBtn"
                                                            style="width: 32px; height: 32px; padding: 0;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <p class="text-primary small mt-2 mb-0">Foto baru</p>
                                            </div>
                                            
                                            <!-- Upload Area -->
                                            <div class="upload-area" id="uploadArea">
                                                <input type="file" 
                                                       class="d-none" 
                                                       id="foto" 
                                                       name="foto" 
                                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                                
                                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                                <p class="mb-1 fw-bold">Upload Foto Alat</p>
                                                <p class="text-muted small mb-2">Klik atau drag & drop file di sini</p>
                                                
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="uploadPhotoBtn">
                                                    <i class="fas fa-upload me-1"></i> Pilih File
                                                </button>
                                                
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle"></i> Format: JPG, PNG, GIF. Maks: 5MB
                                                    </small>
                                                </div>
                                                <div id="fileName" class="small text-success mt-1"></div>
                                            </div>
                                            
                                            @error('foto')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror
                                            
                                            <!-- Hidden field untuk hapus foto -->
                                            @if(isset($laptop) && !empty($laptop->foto))
                                                <input type="hidden" name="remove_foto" id="remove_foto" value="0">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- ================ END FOTO ================ -->
                            </div>
                        
                            <!-- Form/Show Actions -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('admin.laptops.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali
                                </a>
                                
                                @if($mode == 'show')
                                    <div>
                                        <a href="{{ route('admin.laptops.edit', $laptop) }}" class="btn btn-primary me-2">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $laptop->id }}, '{{ $laptop->nama_alat }}')">
                                            <i class="fas fa-trash me-2"></i> Hapus
                                        </button>
                                        <form id="delete-form-{{ $laptop->id }}" action="{{ route('admin.laptops.destroy', $laptop) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                @else
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        {{ $mode == 'edit' ? 'Update' : 'Simpan' }}
                                    </button>
                                @endif
                            </div>
                            
                            @if($mode != 'show')
                                </form>
                            @endif
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
    
    // ================ UPLOAD FOTO ================
    document.addEventListener('DOMContentLoaded', function() {
        const fotoInput = document.getElementById('foto');
        const uploadBtn = document.getElementById('uploadPhotoBtn');
        const uploadArea = document.getElementById('uploadArea');
        const previewContainer = document.getElementById('photoPreviewContainer');
        const photoPreview = document.getElementById('photoPreview');
        const currentContainer = document.getElementById('currentPhotoContainer');
        const fileName = document.getElementById('fileName');
        const removeBtn = document.getElementById('removePhotoBtn');
        const removeHidden = document.getElementById('remove_foto');
        
        if (uploadBtn && fotoInput) {
            // Trigger file input
            uploadBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fotoInput.click();
            });
            
            // Upload area click
            uploadArea.addEventListener('click', function() {
                fotoInput.click();
            });
            
            // File selection
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    fileName.textContent = '✓ ' + file.name;
                    fileName.classList.add('text-success');
                    
                    reader.onload = function(e) {
                        photoPreview.src = e.target.result;
                        previewContainer.style.display = 'block';
                        if (currentContainer) currentContainer.style.display = 'none';
                        uploadArea.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(file);
                    
                    if (removeHidden) removeHidden.value = '0';
                }
            });
            
            // Remove photo
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    fotoInput.value = '';
                    previewContainer.style.display = 'none';
                    if (currentContainer) currentContainer.style.display = 'block';
                    fileName.textContent = '';
                    
                    if (removeHidden) removeHidden.value = '1';
                });
            }
            
            // Drag & Drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, function() {
                    uploadArea.classList.add('bg-light');
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, function() {
                    uploadArea.classList.remove('bg-light');
                }, false);
            });
            
            uploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fotoInput.files = files;
                    const event = new Event('change', { bubbles: true });
                    fotoInput.dispatchEvent(event);
                }
            }, false);
        }
    });
</script>

<style>
    .form-control-plaintext {
        min-height: 38px;
        padding: 6px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    
    textarea.form-control-plaintext {
        min-height: 100px;
    }
    
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        background-color: white;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .upload-area:hover {
        border-color: #3a86ff;
        background-color: #eef2ff;
    }
    
    .no-photo {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
@endsection