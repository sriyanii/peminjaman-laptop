@extends('layouts.app')

@section('title', $mode == 'show' ? 'Detail Alat' : ($mode == 'edit' ? 'Edit Alat' : 'Tambah Alat Baru'))
@section('header-icon', $mode == 'show' ? 'fas fa-tools' : ($mode == 'edit' ? 'fas fa-edit' : 'fas fa-plus'))
@section('header-title', $mode == 'show' ? 'Detail Alat' : ($mode == 'edit' ? 'Edit Alat' : 'Tambah Alat Baru'))

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="{{ $mode == 'show' ? 'fas fa-tools' : ($mode == 'edit' ? 'fas fa-edit text-primary' : 'fas fa-plus text-primary') }} me-2"></i>
                        @if($mode == 'show')
                            Detail Alat: {{ $laptop->merk }} {{ $laptop->model }}
                        @elseif($mode == 'edit')
                            Form Edit Alat: {{ $laptop->merk }} {{ $laptop->model }}
                        @else
                            Form Tambah Alat Baru
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    
                    @if($mode == 'show')
                        <!-- ================ SHOW MODE ================ -->
                        <div class="row">
                            <!-- FOTO ALAT -->
                            <div class="col-12 text-center mb-4">
                                @if(!empty($laptop->gambar))
                                    <img src="{{ asset($laptop->gambar) }}" 
                                         alt="{{ $laptop->merk }} {{ $laptop->model }}"
                                         class="img-thumbnail shadow"
                                         style="max-width: 250px; max-height: 250px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded p-4 d-inline-block">
                                        <i class="fas fa-camera text-muted fa-4x"></i>
                                        <p class="text-muted mt-2 mb-0">Belum ada foto</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- DETAIL ALAT -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Kode Alat</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">#LAP-{{ str_pad($laptop->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Nama Alat</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->merk }} {{ $laptop->model }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Merk</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->merk }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Model</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->model }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Processor</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->processor }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">RAM</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->ram }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Storage</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->storage }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Serial Number</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->serial_number ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ ucfirst($laptop->status) }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Kondisi</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ str_replace('_', ' ', ucfirst($laptop->kondisi)) }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Tahun Pembelian</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->tahun_pembelian ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Lokasi</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->lokasi ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Warna</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->warna ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">OS</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->os ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Kondisi Baterai</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->baterai_kondisi ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Garansi</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->garansi ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Garansi Berakhir</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">{{ $laptop->garansi_berakhir ? \Carbon\Carbon::parse($laptop->garansi_berakhir)->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Harga Beli</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">Rp {{ number_format($laptop->harga_beli ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Harga Sewa Harian</label>
                                    <div class="form-control-plaintext bg-light p-2 rounded">Rp {{ number_format($laptop->harga_sewa_harian ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted">Keterangan</label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">{{ $laptop->keterangan ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ACTION BUTTONS -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('petugas.alat.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <div>
                                <a href="{{ route('petugas.alat.edit', $laptop->id) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-2"></i> Edit Alat
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $laptop->id }}, '{{ $laptop->merk }} {{ $laptop->model }}')">
                                    <i class="fas fa-trash me-2"></i> Hapus
                                </button>
                                <form id="delete-form-{{ $laptop->id }}" action="{{ route('petugas.alat.destroy', $laptop->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>

                    @else
                        <!-- ================ CREATE/EDIT MODE ================ -->
                        <form method="POST" 
                              action="{{ $mode == 'edit' ? route('petugas.alat.update', $laptop->id) : route('petugas.alat.store') }}" 
                              enctype="multipart/form-data">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif
                            
                            <div class="row">
                                <!-- KOLOM KIRI: FORM DATA -->
                                <div class="col-md-8">
                                    <!-- Kode Alat (auto) -->
                                    <div class="mb-3">
                                        <label class="form-label">Kode Alat</label>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               value="{{ $mode == 'create' ? '(Otomatis)' : '#LAP-' . str_pad($laptop->id, 5, '0', STR_PAD_LEFT) }}"
                                               readonly>
                                        <small class="text-muted">Kode alat akan dibuat otomatis</small>
                                    </div>
                                    
                                    <!-- Nama Alat (auto) -->
                                    <div class="mb-3">
                                        <label class="form-label">Nama Alat</label>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               value="{{ $mode == 'create' ? '(Akan digabung dari Merk + Model)' : $laptop->merk . ' ' . $laptop->model }}"
                                               readonly>
                                    </div>
                                    
                                    <!-- Merk & Model -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="merk" class="form-label">Merk <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('merk') is-invalid @enderror" 
                                                   id="merk" 
                                                   name="merk" 
                                                   value="{{ old('merk', $laptop->merk ?? '') }}"
                                                   required>
                                            @error('merk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('model') is-invalid @enderror" 
                                                   id="model" 
                                                   name="model" 
                                                   value="{{ old('model', $laptop->model ?? '') }}"
                                                   required>
                                            @error('model')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Processor, RAM, Storage -->
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="processor" class="form-label">Processor <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('processor') is-invalid @enderror" 
                                                   id="processor" 
                                                   name="processor" 
                                                   value="{{ old('processor', $laptop->processor ?? '') }}"
                                                   required>
                                            @error('processor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="ram" class="form-label">RAM <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('ram') is-invalid @enderror" 
                                                   id="ram" 
                                                   name="ram" 
                                                   value="{{ old('ram', $laptop->ram ?? '') }}"
                                                   required>
                                            @error('ram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="storage" class="form-label">Storage <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('storage') is-invalid @enderror" 
                                                   id="storage" 
                                                   name="storage" 
                                                   value="{{ old('storage', $laptop->storage ?? '') }}"
                                                   required>
                                            @error('storage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Serial Number -->
                                    <div class="mb-3">
                                        <label for="serial_number" class="form-label">Serial Number <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('serial_number') is-invalid @enderror" 
                                               id="serial_number" 
                                               name="serial_number" 
                                               value="{{ old('serial_number', $laptop->serial_number ?? '') }}"
                                               required>
                                        @error('serial_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Status & Kondisi -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
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
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                            <select class="form-control @error('kondisi') is-invalid @enderror" 
                                                    id="kondisi" 
                                                    name="kondisi" 
                                                    required>
                                                <option value="">Pilih Kondisi</option>
                                                <option value="baik" {{ old('kondisi', $laptop->kondisi ?? '') == 'baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="rusak_ringan" {{ old('kondisi', $laptop->kondisi ?? '') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                <option value="rusak_berat" {{ old('kondisi', $laptop->kondisi ?? '') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                            </select>
                                            @error('kondisi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Tahun Pembelian & Lokasi -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="tahun_pembelian" class="form-label">Tahun Pembelian <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   class="form-control @error('tahun_pembelian') is-invalid @enderror" 
                                                   id="tahun_pembelian" 
                                                   name="tahun_pembelian" 
                                                   min="2000" 
                                                   max="{{ date('Y') }}"
                                                   value="{{ old('tahun_pembelian', $laptop->tahun_pembelian ?? '') }}"
                                                   required>
                                            @error('tahun_pembelian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="lokasi" class="form-label">Lokasi</label>
                                            <input type="text" 
                                                   class="form-control @error('lokasi') is-invalid @enderror" 
                                                   id="lokasi" 
                                                   name="lokasi" 
                                                   value="{{ old('lokasi', $laptop->lokasi ?? '') }}">
                                            @error('lokasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Warna & OS -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="warna" class="form-label">Warna</label>
                                            <input type="text" 
                                                   class="form-control @error('warna') is-invalid @enderror" 
                                                   id="warna" 
                                                   name="warna" 
                                                   value="{{ old('warna', $laptop->warna ?? '') }}">
                                            @error('warna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="os" class="form-label">OS</label>
                                            <input type="text" 
                                                   class="form-control @error('os') is-invalid @enderror" 
                                                   id="os" 
                                                   name="os" 
                                                   value="{{ old('os', $laptop->os ?? '') }}">
                                            @error('os')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Baterai Kondisi & Garansi -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="baterai_kondisi" class="form-label">Kondisi Baterai</label>
                                            <input type="text" 
                                                   class="form-control @error('baterai_kondisi') is-invalid @enderror" 
                                                   id="baterai_kondisi" 
                                                   name="baterai_kondisi" 
                                                   value="{{ old('baterai_kondisi', $laptop->baterai_kondisi ?? '') }}">
                                            @error('baterai_kondisi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="garansi" class="form-label">Garansi</label>
                                            <input type="text" 
                                                   class="form-control @error('garansi') is-invalid @enderror" 
                                                   id="garansi" 
                                                   name="garansi" 
                                                   value="{{ old('garansi', $laptop->garansi ?? '') }}">
                                            @error('garansi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Garansi Berakhir & Harga Beli -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="garansi_berakhir" class="form-label">Garansi Berakhir</label>
                                            <input type="date" 
                                                   class="form-control @error('garansi_berakhir') is-invalid @enderror" 
                                                   id="garansi_berakhir" 
                                                   name="garansi_berakhir" 
                                                   value="{{ old('garansi_berakhir', $laptop->garansi_berakhir ?? '') }}">
                                            @error('garansi_berakhir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="harga_beli" class="form-label">Harga Beli</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" 
                                                       class="form-control @error('harga_beli') is-invalid @enderror" 
                                                       id="harga_beli" 
                                                       name="harga_beli" 
                                                       value="{{ old('harga_beli', $laptop->harga_beli ?? '') }}">
                                            </div>
                                            @error('harga_beli')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Harga Sewa Harian -->
                                    <div class="mb-3">
                                        <label for="harga_sewa_harian" class="form-label">Harga Sewa Harian</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('harga_sewa_harian') is-invalid @enderror" 
                                                   id="harga_sewa_harian" 
                                                   name="harga_sewa_harian" 
                                                   value="{{ old('harga_sewa_harian', $laptop->harga_sewa_harian ?? '') }}">
                                        </div>
                                        @error('harga_sewa_harian')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Keterangan -->
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                                  id="keterangan" 
                                                  name="keterangan" 
                                                  rows="3">{{ old('keterangan', $laptop->keterangan ?? '') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- ================ KOLOM KANAN: UPLOAD FOTO ================ -->
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-3">Foto Alat</h6>
                                            
                                            <!-- Preview Foto Saat Ini -->
                                            <div id="currentPhoto" class="mb-3">
                                                @if($mode == 'edit' && !empty($laptop->gambar))
                                                    <img src="{{ asset($laptop->gambar) }}" 
                                                         class="img-thumbnail mb-2"
                                                         style="max-width: 100%; max-height: 150px; object-fit: cover;">
                                                    <p class="text-muted small">Foto saat ini</p>
                                                @else
                                                    <div class="bg-white p-3 rounded border">
                                                        <i class="fas fa-camera text-muted fa-3x"></i>
                                                        <p class="text-muted mt-2">Belum ada foto</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Preview Foto Baru -->
                                            <div id="newPhotoPreview" class="mb-3" style="display: none;">
                                                <img id="preview" src="#" class="img-thumbnail mb-2" style="max-width: 100%; max-height: 150px; object-fit: cover;">
                                                <p class="text-primary small">Foto baru</p>
                                            </div>
                                            
                                            <!-- INPUT FILE -->
                                            <div class="mb-3">
                                                <label for="gambar" class="form-label fw-semibold">Pilih File</label>
                                                <input type="file" 
                                                       class="form-control" 
                                                       id="gambar" 
                                                       name="gambar" 
                                                       accept="image/*"
                                                       onchange="previewFile(this)">
                                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                                            </div>
                                            
                                            @error('gambar')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                            
                                            @if($mode == 'edit' && !empty($laptop->gambar))
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="hapus_gambar" id="hapus_gambar" value="1">
                                                <label class="form-check-label text-danger" for="hapus_gambar">
                                                    Hapus foto saat ini
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FORM ACTIONS -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('petugas.alat.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>
                                    {{ $mode == 'edit' ? 'Update Alat' : 'Simpan Alat' }}
                                </button>
                            </div>
                        </form>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Fungsi untuk preview foto sebelum upload
    function previewFile(input) {
        const preview = document.getElementById('preview');
        const newPreviewDiv = document.getElementById('newPhotoPreview');
        const currentPhoto = document.getElementById('currentPhoto');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                newPreviewDiv.style.display = 'block';
                if (currentPhoto) currentPhoto.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
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
@endsection