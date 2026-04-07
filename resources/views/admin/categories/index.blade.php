@extends('layouts.app')

@section('title', 'Manajemen Kategori')
@section('header-icon', 'fas fa-tags')
@section('header-title', 'Manajemen Kategori')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <!-- ====================== TABEL KATEGORI ====================== -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Daftar Kategori
                </h5>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Cari kategori..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i> Tambah Kategori
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Dibuat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $category->nama_kategori }}</div>
                                @if($category->slug)
                                    <small class="text-muted">{{ $category->slug }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $category->deskripsi ? Str::limit($category->deskripsi, 50) : '-' }}
                            </td>
                            <td>
                                {{ $category->created_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ $category->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-info" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- PERBAIKAN: Ganti $laptop dengan $category -->
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->nama_kategori) }}')" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <!-- PERBAIKAN: Ganti route tools dengan categories dan $laptop dengan $category -->
                                    <form id="delete-form-{{ $category->id }}" 
                                          action="{{ route('admin.categories.destroy', $category) }}" 
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
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-tags fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada kategori</p>
                                    <small>Klik "Tambah Kategori" untuk menambahkan data baru</small>
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
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
                </div>
                <div>
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi konfirmasi delete
    function confirmDelete(categoryId, categoryName) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Kategori <strong>${categoryName}</strong> akan dihapus permanen!<br>
                  <small class="text-danger">Semua alat dalam kategori ini akan kehilangan kategori.</small>`,
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
    
    // Tampilkan notifikasi dari session
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
@endsection