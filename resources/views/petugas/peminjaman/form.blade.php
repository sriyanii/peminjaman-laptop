{{-- resources/views/petugas/peminjaman/form.blade.php --}}
@extends('layouts.app')

@section('title', isset($peminjaman) ? 'Edit Peminjaman' : 'Tambah Peminjaman')
@section('header-icon', 'fas fa-laptop')
@section('header-title', isset($peminjaman) ? 'Edit Peminjaman' : 'Tambah Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas {{ isset($peminjaman) ? 'fa-edit' : 'fa-plus-circle' }} text-primary me-2"></i>
                        {{ isset($peminjaman) ? 'Edit Peminjaman' : 'Form Peminjaman Laptop' }}
                    </h5>
                </div>
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($peminjaman) ? route('petugas.peminjaman.update', $peminjaman->id) : route('petugas.peminjaman.store') }}">
                        @csrf
                        @if(isset($peminjaman))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Peminjam <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Pilih Peminjam</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                            {{ old('user_id', isset($peminjaman) ? $peminjaman->user_id : '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="laptop_id" class="form-label">Laptop <span class="text-danger">*</span></label>
                                <select name="laptop_id" id="laptop_id" class="form-select @error('laptop_id') is-invalid @enderror" required>
                                    <option value="">Pilih Laptop</option>
                                    @foreach($laptops as $laptop)
                                        <option value="{{ $laptop->id }}" 
                                            data-harga="{{ $laptop->harga_sewa_harian ?? 50000 }}"
                                            {{ old('laptop_id', isset($peminjaman) ? $peminjaman->laptop_id : '') == $laptop->id ? 'selected' : '' }}>
                                            {{ $laptop->merk }} {{ $laptop->model }} 
                                            @if($laptop->serial_number)
                                                - {{ $laptop->serial_number }}
                                            @endif
                                            (Rp {{ number_format($laptop->harga_sewa_harian ?? 50000, 0, ',', '.') }}/hari)
                                        </option>
                                    @endforeach
                                </select>
                                @error('laptop_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                       id="tanggal_pinjam" 
                                       name="tanggal_pinjam" 
                                       value="{{ old('tanggal_pinjam', isset($peminjaman) ? $peminjaman->tanggal_pinjam : date('Y-m-d')) }}"
                                       required>
                                @error('tanggal_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_kembali_rencana" class="form-label">Rencana Kembali <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror" 
                                       id="tanggal_kembali_rencana" 
                                       name="tanggal_kembali_rencana" 
                                       value="{{ old('tanggal_kembali_rencana', isset($peminjaman) ? $peminjaman->tanggal_kembali_rencana : '') }}"
                                       required>
                                @error('tanggal_kembali_rencana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal rencana pengembalian laptop</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tujuan" class="form-label">Tujuan Peminjaman <span class="text-danger">*</span></label>
                                <select name="tujuan" id="tujuan" class="form-select @error('tujuan') is-invalid @enderror" required>
                                    <option value="">Pilih Tujuan</option>
                                    <option value="meeting" {{ old('tujuan', isset($peminjaman) ? $peminjaman->tujuan : '') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="presentasi" {{ old('tujuan', isset($peminjaman) ? $peminjaman->tujuan : '') == 'presentasi' ? 'selected' : '' }}>Presentasi</option>
                                    <option value="training" {{ old('tujuan', isset($peminjaman) ? $peminjaman->tujuan : '') == 'training' ? 'selected' : '' }}>Training</option>
                                    <option value="work_from_home" {{ old('tujuan', isset($peminjaman) ? $peminjaman->tujuan : '') == 'work_from_home' ? 'selected' : '' }}>Work From Home</option>
                                    <option value="proyek" {{ old('tujuan', isset($peminjaman) ? $peminjaman->tujuan : '') == 'proyek' ? 'selected' : '' }}>Proyek</option>
                                    <option value="lainnya" {{ old('tujuan', isset($peminjaman) ? $peminjaman->tujuan : '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="lama_hari" class="form-label">Lama Pinjam</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="lama_hari" 
                                       readonly
                                       style="background-color: #f8f9fa; cursor: not-allowed;">
                                <small class="text-muted">Otomatis terhitung dari tanggal pinjam dan rencana kembali</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga_per_hari" class="form-label">Harga Sewa per Hari</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="harga_per_hari" 
                                       readonly
                                       style="background-color: #f8f9fa; cursor: not-allowed;">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="total_harga" class="form-label">Total Harga Sewa</label>
                                <input type="text" 
                                       class="form-control fw-bold text-primary" 
                                       id="total_harga" 
                                       readonly
                                       style="background-color: #f8f9fa; cursor: not-allowed;">
                                <input type="hidden" name="harga_sewa" id="total_harga_hidden">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3" 
                                      placeholder="Contoh: Untuk keperluan presentasi client, perlu software khusus, dll.">{{ old('keterangan', isset($peminjaman) ? $peminjaman->keterangan : '') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> {{ isset($peminjaman) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Data harga laptop
let laptopHarga = {};

// Inisialisasi data harga laptop
document.querySelectorAll('#laptop_id option').forEach(function(option) {
    if (option.value) {
        laptopHarga[option.value] = parseFloat(option.dataset.harga) || 50000;
    }
});

// Fungsi menghitung lama hari
function hitungLamaHari() {
    var tglPinjam = document.getElementById('tanggal_pinjam').value;
    var tglKembali = document.getElementById('tanggal_kembali_rencana').value;
    
    if (tglPinjam && tglKembali) {
        var pinjam = new Date(tglPinjam);
        var kembali = new Date(tglKembali);
        
        if (kembali >= pinjam) {
            var diffTime = Math.abs(kembali - pinjam);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            document.getElementById('lama_hari').value = diffDays + ' hari';
            return diffDays;
        } else {
            document.getElementById('lama_hari').value = 'Tanggal tidak valid';
            return 0;
        }
    }
    return 0;
}

// Fungsi menghitung total harga
function hitungTotalHarga() {
    var laptopId = document.getElementById('laptop_id').value;
    var hargaPerHari = laptopHarga[laptopId] || 0;
    var lamaHari = hitungLamaHari();
    
    var totalHarga = hargaPerHari * lamaHari;
    
    var formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
    
    document.getElementById('harga_per_hari').value = formatter.format(hargaPerHari);
    document.getElementById('total_harga').value = formatter.format(totalHarga);
    document.getElementById('total_harga_hidden').value = totalHarga;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    var tglPinjam = document.getElementById('tanggal_pinjam');
    var tglKembali = document.getElementById('tanggal_kembali_rencana');
    var laptopSelect = document.getElementById('laptop_id');
    
    tglPinjam.addEventListener('change', function() {
        tglKembali.min = this.value;
        if (tglKembali.value && tglKembali.value < this.value) {
            tglKembali.value = this.value;
        }
        hitungTotalHarga();
    });
    
    tglKembali.addEventListener('change', function() {
        hitungTotalHarga();
    });
    
    laptopSelect.addEventListener('change', function() {
        hitungTotalHarga();
    });
    
    // Hitung awal
    hitungTotalHarga();
});
</script>
@endsection