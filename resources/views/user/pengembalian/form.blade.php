@extends('layouts.app')

@section('title', 'Form Pengembalian')
@section('header-icon', 'fas fa-undo-alt')
@section('header-title', 'Form Pengembalian Alat')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-peminjaman.css') }}">
@endpush
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
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Info Peminjaman -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Kode</strong></td>
                                        <td>: #{{ $peminjaman->kode_peminjaman ?? 'PJ-'.$peminjaman->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alat</strong></td>
                                        <td>: {{ $peminjaman->laptop->nama_alat ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Merk/Type</strong></td>
                                        <td>: {{ $peminjaman->laptop->merk ?? '' }} {{ $peminjaman->laptop->type ?? '' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Tgl Pinjam</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Rencana Kembali</strong></td>
                                        <td>: 
                                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}
                                            @if($hari_terlambat > 0)
                                                <span class="badge bg-danger ms-2">Terlambat {{ $hari_terlambat }} hari</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Estimasi Denda (Info Saja) -->
                    @if($estimasi_denda > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <strong>Estimasi Denda Keterlambatan:</strong> Rp {{ number_format($estimasi_denda, 0, ',', '.') }}
                            <br>
                            <small class="text-muted">*Denda final akan ditentukan petugas setelah pengecekan</small>
                        </div>
                    @endif
                    
                    <form action="{{ route('user.pengembalian.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                        <div class="mb-3">
                            <label for="tanggal_kembali" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                   id="tanggal_kembali" name="tanggal_kembali" 
                                   value="{{ old('tanggal_kembali', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            <small class="text-muted">Tanggal saat Anda mengembalikan alat</small>
                            @error('tanggal_kembali')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi Alat <span class="text-danger">*</span></label>
                            <select class="form-select @error('kondisi') is-invalid @enderror" 
                                    id="kondisi" name="kondisi" required>
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

                        <!-- Info Denda Kerusakan -->
                        <div id="infoDendaKerusakan" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <strong>Estimasi Denda Kerusakan:</strong> 
                            <span id="dendaKerusakanText">Rp 0</span>
                            <br>
                            <small class="text-muted">*Denda final akan ditentukan petugas setelah pengecekan</small>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="3" 
                                      placeholder="Contoh: Keyboard sedikit macet, body lecet, baterai cepat habis...">{{ old('catatan') }}</textarea>
                            <small class="text-muted">Tambahkan catatan jika diperlukan, misal kondisi kerusakan</small>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi:</strong> Setelah mengirim, pengembalian akan menunggu konfirmasi petugas. 
                            Anda akan diarahkan ke halaman "Peminjaman Saya".
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.pengembalian.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Ajukan Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kondisiSelect = document.getElementById('kondisi');
    const infoDendaKerusakan = document.getElementById('infoDendaKerusakan');
    const dendaKerusakanText = document.getElementById('dendaKerusakanText');
    
    function hitungDendaKerusakan() {
        const kondisi = kondisiSelect.value;
        let denda = 0;
        
        switch(kondisi) {
            case 'rusak_ringan':
                denda = 50000;
                break;
            case 'rusak_berat':
                denda = 200000;
                break;
            case 'hilang':
                denda = 1000000;
                break;
            default:
                denda = 0;
        }
        
        if (denda > 0) {
            infoDendaKerusakan.style.display = 'block';
            dendaKerusakanText.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(denda);
        } else {
            infoDendaKerusakan.style.display = 'none';
        }
    }
    
    kondisiSelect.addEventListener('change', hitungDendaKerusakan);
    
    // Hitung saat pertama load jika ada nilai
    if (kondisiSelect.value) {
        hitungDendaKerusakan();
    }
    
    // Set max date untuk tanggal kembali
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_kembali').setAttribute('max', today);
});
</script>


@endsection