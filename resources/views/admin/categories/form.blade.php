@extends('layouts.app')

@section('title', $mode == 'show' ? 'Detail Kategori: ' . $category->nama_kategori : ($mode == 'edit' ? 'Edit Kategori: ' . $category->nama_kategori : 'Tambah Kategori Baru'))
@section('header-icon', 'fas fa-tags')
@section('header-title', $mode == 'show' ? 'Detail Kategori' : ($mode == 'edit' ? 'Edit Kategori' : 'Tambah Kategori Baru'))


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags me-2"></i>
                        @if($mode == 'show')
                            Detail Kategori: {{ $category->nama_kategori }}
                        @elseif($mode == 'edit')
                            Form Edit Kategori
                        @else
                            Form Tambah Kategori Baru
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    
                    @if($mode == 'show')
                        <!-- SHOW MODE: Display Category Details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kode Kategori</label>
                                <div class="form-control-plaintext fw-bold">{{ $category->kode_kategori }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nama Kategori</label>
                                <div class="form-control-plaintext fw-bold">{{ $category->nama_kategori }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Deskripsi</label>
                                <div class="form-control-plaintext">{{ $category->deskripsi ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Dibuat</label>
                                <div class="form-control-plaintext">{{ $category->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Terakhir Update</label>
                                <div class="form-control-plaintext">{{ $category->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                    @else
                        <!-- CREATE/EDIT MODE: Form Input -->
                        <form method="POST" action="{{ $mode == 'edit' ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif
                            
                            <div class="row">
                                <!-- Kode Kategori -->
                                <div class="col-md-6 mb-3">
                                    <label for="kode_kategori" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('kode_kategori') is-invalid @enderror" 
                                           id="kode_kategori" 
                                           name="kode_kategori" 
                                           value="{{ old('kode_kategori', $category->kode_kategori ?? '') }}"
                                           required
                                           maxlength="10"
                                           placeholder="Contoh: LP, PRJ, TAB">
                                    @error('kode_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maksimal 10 karakter, unik</small>
                                </div>
                                
                                <!-- Nama Kategori -->
                                <div class="col-md-6 mb-3">
                                    <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_kategori') is-invalid @enderror" 
                                           id="nama_kategori" 
                                           name="nama_kategori" 
                                           value="{{ old('nama_kategori', $category->nama_kategori ?? '') }}"
                                           required
                                           maxlength="50"
                                           placeholder="Contoh: Laptop, Projector, Tablet">
                                    @error('nama_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maksimal 50 karakter</small>
                                </div>
                                
                                <!-- Deskripsi -->
                                <div class="col-12 mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                              id="deskripsi" 
                                              name="deskripsi" 
                                              rows="3"
                                              placeholder="Deskripsi kategori">{{ old('deskripsi', $category->deskripsi ?? '') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali
                                </a>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    {{ $mode == 'edit' ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    @endif
                    
                    <!-- Show Mode Actions -->
                    @if($mode == 'show')
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            
                            <div>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $category->id }}, '{{ $category->nama_kategori }}')">
                                    <i class="fas fa-trash me-2"></i> Hapus
                                </button>
                                <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
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
    function confirmDelete(categoryId, categoryName) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Kategori <strong>${categoryName}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${categoryId}`).submit();
            }
        });
    }
</script>


@endsection