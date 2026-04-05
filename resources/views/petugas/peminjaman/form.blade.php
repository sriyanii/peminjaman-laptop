@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('header-icon', 'fas fa-undo-alt')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-undo-alt text-primary me-2"></i>
                        Form Pengembalian Alat
                    </h5>
                </div>
                <div class="card-body">
                    
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

                    <!-- Info Peminjaman -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Kode Peminjaman:</strong> #{{ $peminjaman->kode_peminjaman ?? 'PJ-'.$peminjaman->id }}<br>
                                <strong>Peminjam:</strong> {{ $peminjaman->user->name }} ({{ $peminjaman->user->email }})<br>
                                <strong>Alat:</strong> {{ $peminjaman->laptop->nama_alat }} ({{ $peminjaman->laptop->kode_alat }})<br>
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $peminjaman->status == 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal Pinjam:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}<br>
                                <strong>Tanggal Rencana Kembali:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}<br>
                                @php
                                    $today = \Carbon\Carbon::now();
                                    $rencana = \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana);
                                    $hari_terlambat = $today > $rencana ? $rencana->diffInDays($today) : 0;
                                @endphp
                                @if($hari_terlambat > 0)
                                    <span class="text-danger">
                                        <strong>Terlambat:</strong> {{ $hari_terlambat }} hari
                                    </span>
                                @else
                                    <span class="text-success">
                                        <strong>Tepat waktu</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" id="formPengembalian">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_kembali" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                       id="tanggal_kembali" 
                                       name="tanggal_kembali" 
                                       value="{{ old('tanggal_kembali', now()->format('Y-m-d')) }}"
                                       required>
                                @error('tanggal_kembali')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal maksimal: hari ini</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="kondisi" class="form-label">Kondisi Barang <span class="text-danger">*</span></label>
                                <select class="form-control @error('kondisi') is-invalid @enderror" 
                                        id="kondisi" 
                                        name="kondisi" 
                                        required
                                        onchange="hitungDenda()">
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

                        <!-- ================ SECTION DENDA ================ -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-money-bill-wave text-warning me-2"></i>Detail Denda</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Denda Keterlambatan (READ ONLY) -->
                                    <div class="col-md-4 mb-3">
                                        <label for="denda_terlambat" class="form-label">Denda Keterlambatan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('denda_terlambat') is-invalid @enderror" 
                                                   id="denda_terlambat" 
                                                   name="denda_terlambat" 
                                                   value="{{ old('denda_terlambat', $hari_terlambat * 10000) }}"
                                                   min="0"
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <small class="text-muted">Rp 10.000 × <span id="hari_terlambat_text">{{ $hari_terlambat }}</span> hari terlambat</small>
                                    </div>

                                    <!-- Denda Kerusakan (READ ONLY - OTOMATIS) -->
                                    <div class="col-md-4 mb-3">
                                        <label for="denda_kerusakan" class="form-label">Denda Kerusakan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('denda_kerusakan') is-invalid @enderror" 
                                                   id="denda_kerusakan" 
                                                   name="denda_kerusakan" 
                                                   value="{{ old('denda_kerusakan', 0) }}"
                                                   min="0"
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <small class="text-muted">
                                            <span id="ket_kerusakan">Rusak ringan: Rp 50.000 | Rusak berat: Rp 200.000</span>
                                        </small>
                                    </div>

                                    <!-- Denda Hilang (READ ONLY - OTOMATIS) -->
                                    <div class="col-md-4 mb-3">
                                        <label for="denda_hilang" class="form-label">Denda Hilang</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('denda_hilang') is-invalid @enderror" 
                                                   id="denda_hilang" 
                                                   name="denda_hilang" 
                                                   value="{{ old('denda_hilang', 0) }}"
                                                   min="0"
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                        </div>
                                        <small class="text-muted">Jika barang hilang: Rp 1.000.000</small>
                                    </div>
                                </div>

                                <!-- Total Denda -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="alert alert-info mb-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">TOTAL DENDA:</span>
                                                <span class="fw-bold text-danger fs-5" id="total_denda_text">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ================ END SECTION DENDA ================ -->

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="3" 
                                      placeholder="Contoh: Ada goresan di bagian atas, baterai sudah tidak tahan lama, dll.">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Proses Pengembalian
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
    // Fungsi untuk menghitung denda berdasarkan kondisi
    function hitungDenda() {
        const kondisi = document.getElementById('kondisi').value;
        const dendaKerusakan = document.getElementById('denda_kerusakan');
        const dendaHilang = document.getElementById('denda_hilang');
        
        // Reset nilai
        dendaKerusakan.value = 0;
        dendaHilang.value = 0;
        
        // Set default denda berdasarkan kondisi
        switch(kondisi) {
            case 'rusak_ringan':
                dendaKerusakan.value = 50000;
                break;
            case 'rusak_berat':
                dendaKerusakan.value = 200000;
                break;
            case 'hilang':
                dendaHilang.value = 1000000;
                break;
            default:
                // Baik atau tidak dipilih, tetap 0
                break;
        }
        
        hitungTotal();
    }
    
    // Fungsi untuk menghitung total denda
    function hitungTotal() {
        const dendaTerlambat = parseFloat(document.getElementById('denda_terlambat').value) || 0;
        const dendaKerusakan = parseFloat(document.getElementById('denda_kerusakan').value) || 0;
        const dendaHilang = parseFloat(document.getElementById('denda_hilang').value) || 0;
        
        const total = dendaTerlambat + dendaKerusakan + dendaHilang;
        
        // Format rupiah
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
        
        document.getElementById('total_denda_text').innerHTML = formatter.format(total);
    }
    
    // Fungsi untuk menghitung ulang denda keterlambatan berdasarkan tanggal
    function hitungUlangDendaTerlambat() {
        const tglRencana = new Date('{{ $peminjaman->tanggal_kembali_rencana }}');
        const tglKembali = new Date(document.getElementById('tanggal_kembali').value);
        const dendaTerlambatInput = document.getElementById('denda_terlambat');
        const hariTerlambatText = document.getElementById('hari_terlambat_text');
        
        if (tglKembali > tglRencana) {
            // Hitung selisih hari
            const diffTime = Math.abs(tglKembali - tglRencana);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Update denda dan teks keterangan
            dendaTerlambatInput.value = diffDays * 10000;
            hariTerlambatText.textContent = diffDays;
        } else {
            dendaTerlambatInput.value = 0;
            hariTerlambatText.textContent = '0';
        }
        
        hitungTotal();
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Set tanggal maksimal hari ini
        var today = new Date().toISOString().split('T')[0];
        var tanggalKembali = document.getElementById('tanggal_kembali');
        
        // Konversi tanggal pinjam ke format YYYY-MM-DD
        var tanggalPinjam = '{{ $peminjaman->tanggal_pinjam }}';
        var tglPinjamObj = new Date(tanggalPinjam);
        var tglPinjamStr = tglPinjamObj.toISOString().split('T')[0];
        
        if (tanggalKembali) {
            tanggalKembali.max = today; // Maksimal hari ini
            tanggalKembali.min = tglPinjamStr; // Minimal tanggal pinjam
        }
        
        // Hitung total awal
        hitungTotal();
        
        // Event listener untuk perubahan tanggal kembali
        tanggalKembali.addEventListener('change', hitungUlangDendaTerlambat);
        
        // Event listener untuk perubahan kondisi
        document.getElementById('kondisi').addEventListener('change', hitungDenda);
        
        // Jalankan hitungDenda sekali di awal untuk mengisi nilai default jika kondisi sudah terpilih
        if (document.getElementById('kondisi').value) {
            hitungDenda();
        }
    });
</script>
@endsection