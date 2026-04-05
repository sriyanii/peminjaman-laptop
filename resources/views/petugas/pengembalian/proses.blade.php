@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('header-icon', 'fas fa-undo-alt')
@section('header-title', 'Proses Pengembalian')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-undo-alt me-2 text-primary"></i>Form Pengembalian</h5>
        </div>
        <div class="card-body">
            <!-- Tampilkan error dengan lebih baik -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tampilkan session error -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Informasi Peminjaman -->
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Kode:</strong> #{{ $peminjaman->kode_peminjaman ?? 'PJ-'.$peminjaman->id }}<br>
                        <strong>Peminjam:</strong> {{ $peminjaman->user->name }}<br>
                        <strong>Laptop:</strong> {{ $peminjaman->laptop->nama_alat ?? $peminjaman->laptop->merk }}
                    </div>
                    <div class="col-md-6">
                        <strong>Tgl Pinjam:</strong> {{ Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}<br>
                        <strong>Rencana Kembali:</strong> {{ Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}<br>
                        @php
                            $tglRencana = Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana);
                            $terlambat = now()->startOfDay() > $tglRencana ? $tglRencana->diffInDays(now()) : 0;
                        @endphp
                        @if($terlambat > 0)
                            <span class="text-danger fw-bold">
                                <i class="fas fa-exclamation-circle"></i> Terlambat {{ $terlambat }} hari
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FORM DENGAN ACTION YANG SESUAI ROUTE --}}
            <form method="POST" action="/petugas/pengembalian/{{ $peminjaman->id }}/proses" id="formPengembalian">
                @csrf
                
                {{-- Hidden field untuk id --}}
                <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_kembali" 
                               class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                               value="{{ old('tanggal_kembali', date('Y-m-d')) }}" 
                               max="{{ date('Y-m-d') }}" 
                               required 
                               id="tanggal_kembali">
                        @error('tanggal_kembali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select @error('kondisi') is-invalid @enderror" required id="kondisi">
                            <option value="">Pilih Kondisi</option>
                            <option value="baik" {{ old('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ old('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ old('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="hilang" {{ old('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        @error('kondisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Detail Denda -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Detail Denda</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label>Denda Terlambat (Rp 10.000/hari)</label>
                                <input type="text" class="form-control bg-white" id="denda_terlambat_display" readonly value="Rp 0">
                                <input type="hidden" name="denda_terlambat" id="denda_terlambat" value="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Denda Kerusakan</label>
                                <input type="text" class="form-control bg-white" id="denda_kerusakan_display" readonly value="Rp 0">
                                <input type="hidden" name="denda_kerusakan" id="denda_kerusakan" value="0">
                                <small class="text-muted">Ringan: Rp50k, Berat: Rp200k</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Denda Hilang</label>
                                <input type="text" class="form-control bg-white" id="denda_hilang_display" readonly value="Rp 0">
                                <input type="hidden" name="denda_hilang" id="denda_hilang" value="0">
                                <small class="text-muted">Rp1.000.000</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0 d-flex justify-content-between align-items-center">
                            <strong>Total Denda:</strong>
                            <span class="fw-bold fs-5 text-primary" id="total_denda_display">Rp 0</span>
                            <input type="hidden" name="total_denda" id="total_denda" value="0">
                        </div>
                        
                        <div id="warningMessage" class="mt-2" style="display: none;">
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <span id="warningText"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                              rows="2" placeholder="Catatan pengecekan...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> Proses Pengembalian
                    </button>
                </div>
            </form>
            {{-- TUTUP FORM UTAMA --}}
        </div>
    </div>
</div>

<!-- JavaScript tetap sama -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Form pengembalian siap');
    
    // Ambil elemen yang diperlukan
    const form = document.getElementById('formPengembalian');
    const tglKembaliInput = document.getElementById('tanggal_kembali');
    const kondisiSelect = document.getElementById('kondisi');
    const btnSubmit = document.getElementById('btnSubmit');
    const warningDiv = document.getElementById('warningMessage');
    const warningText = document.getElementById('warningText');
    
    // Cek apakah elemen ditemukan
    console.log('Form:', form ? 'Ditemukan' : 'Tidak ditemukan');
    console.log('Tanggal:', tglKembaliInput ? 'Ditemukan' : 'Tidak ditemukan');
    console.log('Kondisi:', kondisiSelect ? 'Ditemukan' : 'Tidak ditemukan');
    
    // Konstanta denda
    const DENDA_PER_HARI = 10000;
    const DENDA_RUSAK_RINGAN = 50000;
    const DENDA_RUSAK_BERAT = 200000;
    const DENDA_HILANG = 1000000;
    
    // Parse tanggal rencana
    const tglRencanaStr = '{{ $peminjaman->tanggal_kembali_rencana->format("Y-m-d") }}';
    const tglRencana = new Date(tglRencanaStr + 'T00:00:00');
    console.log('Tanggal rencana:', tglRencana);
    
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    function hitungSelisihHari(tgl1, tgl2) {
        const date1 = new Date(tgl1.toDateString());
        const date2 = new Date(tgl2.toDateString());
        const diffTime = Math.abs(date2 - date1);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
    
    function hitungDenda() {
        if (!tglKembaliInput.value) return;
        
        const tglKembali = new Date(tglKembaliInput.value + 'T00:00:00');
        let dendaTerlambat = 0;
        let diffDays = 0;
        
        if (tglKembali > tglRencana) {
            diffDays = hitungSelisihHari(tglRencana, tglKembali);
            dendaTerlambat = diffDays * DENDA_PER_HARI;
        }
        
        const kondisi = kondisiSelect.value;
        let dendaKerusakan = 0;
        let dendaHilang = 0;
        
        switch(kondisi) {
            case 'rusak_ringan':
                dendaKerusakan = DENDA_RUSAK_RINGAN;
                break;
            case 'rusak_berat':
                dendaKerusakan = DENDA_RUSAK_BERAT;
                break;
            case 'hilang':
                dendaHilang = DENDA_HILANG;
                break;
        }
        
        const total = dendaTerlambat + dendaKerusakan + dendaHilang;
        
        // Update display
        document.getElementById('denda_terlambat_display').value = formatRupiah(dendaTerlambat);
        document.getElementById('denda_terlambat').value = dendaTerlambat;
        
        document.getElementById('denda_kerusakan_display').value = formatRupiah(dendaKerusakan);
        document.getElementById('denda_kerusakan').value = dendaKerusakan;
        
        document.getElementById('denda_hilang_display').value = formatRupiah(dendaHilang);
        document.getElementById('denda_hilang').value = dendaHilang;
        
        document.getElementById('total_denda_display').innerText = formatRupiah(total);
        document.getElementById('total_denda').value = total;
        
        // Tampilkan warning
        if (kondisi === 'hilang') {
            warningText.innerText = 'Barang hilang akan dikenakan denda Rp 1.000.000 dan status barang akan diubah menjadi hilang.';
            warningDiv.style.display = 'block';
        } else if (dendaTerlambat > 0) {
            warningText.innerText = `Terlambat ${diffDays} hari dengan denda ${formatRupiah(dendaTerlambat)}.`;
            warningDiv.style.display = 'block';
        } else {
            warningDiv.style.display = 'none';
        }
        
        console.log('Total denda:', total);
    }
    
    // Event listeners
    tglKembaliInput.addEventListener('change', hitungDenda);
    kondisiSelect.addEventListener('change', hitungDenda);
    
    // Hitung denda awal
    hitungDenda();
    
    // Validasi form sebelum submit
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
            
            const tanggal = document.querySelector('[name="tanggal_kembali"]').value;
            const kondisi = kondisiSelect.value;
            const totalDenda = parseInt(document.getElementById('total_denda').value) || 0;
            
            // Validasi
            if (!tanggal) {
                e.preventDefault();
                alert('Tanggal kembali harus diisi!');
                return false;
            }
            
            if (!kondisi) {
                e.preventDefault();
                alert('Silakan pilih kondisi barang terlebih dahulu!');
                return false;
            }
            
            if (totalDenda > 0) {
                const konfirmasi = confirm(`Total denda: ${formatRupiah(totalDenda)}\n\nApakah Anda yakin ingin memproses pengembalian ini?`);
                if (!konfirmasi) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Tampilkan loading
            if (btnSubmit) {
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                btnSubmit.disabled = true;
            }
            
            // Debug data yang dikirim
            const formData = new FormData(form);
            console.log('Data yang dikirim:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            return true;
        });
    }
});
</script>

<style>
.spinner-border {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    vertical-align: text-bottom;
    border: 0.2em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}
</style>
@endsection