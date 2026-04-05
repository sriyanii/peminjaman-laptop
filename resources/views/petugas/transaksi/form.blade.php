@extends('layouts.app')

@section('title', 'Transaksi Denda')
@section('header-title', 'Transaksi Denda')
@section('header-icon', 'fas fa-money-bill-wave')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Form Transaksi Denda
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('petugas.transaksi.store', $peminjaman->id) }}" method="POST" enctype="multipart/form-data" id="transaksiForm">
                        @csrf

                        <!-- Informasi Peminjaman -->
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Peminjaman</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Kode Peminjaman</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                <strong>{{ $peminjaman->kode_peminjaman }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Peminjam</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                <strong>{{ $peminjaman->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $peminjaman->user->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Alat yang Dipinjam</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                <strong>{{ $peminjaman->laptop->nama_alat ?? 'Alat' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $peminjaman->laptop->merk ?? '' }} {{ $peminjaman->laptop->type ?? '' }}
                                                    @if($peminjaman->laptop->serial_number ?? false)
                                                        (SN: {{ $peminjaman->laptop->serial_number }})
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tanggal Pinjam</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tanggal Kembali Rencana</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->translatedFormat('d F Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tanggal Kembali Aktual</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                @if($peminjaman->tanggal_kembali)
                                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                                                @else
                                                    <span class="text-danger">Belum dikembalikan</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                @switch($peminjaman->status)
                                                    @case('selesai')
                                                        <span class="badge bg-success">Selesai</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-info">Disetujui</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-warning">{{ ucfirst($peminjaman->status) }}</span>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cek Kondisi Barang -->
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-search me-2"></i>Cek Kondisi Barang</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            <strong>Petunjuk:</strong> Periksa kondisi alat yang dikembalikan dengan teliti. 
                                            Pilih kondisi yang sesuai dan berikan catatan jika diperlukan.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold required">Kondisi Barang</label>
                                            <div class="row">
                                                @php
                                                    $kondisiOptions = [
                                                        'baik' => [
                                                            'icon' => 'fas fa-check-circle',
                                                            'color' => 'success',
                                                            'label' => 'Baik',
                                                            'description' => 'Tidak ada kerusakan, berfungsi normal'
                                                        ],
                                                        'rusak_ringan' => [
                                                            'icon' => 'fas fa-exclamation-triangle',
                                                            'color' => 'warning',
                                                            'label' => 'Rusak Ringan',
                                                            'description' => 'Kerusakan kecil (gosong, lecet)'
                                                        ],
                                                        'rusak_berat' => [
                                                            'icon' => 'fas fa-times-circle',
                                                            'color' => 'danger',
                                                            'label' => 'Rusak Berat',
                                                            'description' => 'Kerusakan serius, perlu perbaikan'
                                                        ],
                                                        'hilang' => [
                                                            'icon' => 'fas fa-ban',
                                                            'color' => 'dark',
                                                            'label' => 'Hilang',
                                                            'description' => 'Alat tidak dikembalikan'
                                                        ]
                                                    ];
                                                @endphp

                                                @foreach($kondisiOptions as $value => $option)
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check card kondisi-card" 
                                                         data-kondisi="{{ $value }}"
                                                         onclick="selectKondisi('{{ $value }}', {{ $option == 'hilang' ? 500000 : ($option == 'rusak_berat' ? 300000 : ($option == 'rusak_ringan' ? 100000 : 0)) }})">
                                                        <input type="radio" 
                                                               class="form-check-input" 
                                                               name="kondisi_barang" 
                                                               id="kondisi_{{ $value }}" 
                                                               value="{{ $value }}"
                                                               {{ old('kondisi_barang') == $value ? 'checked' : '' }}
                                                               required>
                                                        <div class="card-body text-center">
                                                            <div class="mb-2">
                                                                <i class="{{ $option['icon'] }} fa-2x text-{{ $option['color'] }}"></i>
                                                            </div>
                                                            <label class="form-check-label fw-bold" for="kondisi_{{ $value }}">
                                                                {{ $option['label'] }}
                                                            </label>
                                                            <p class="text-muted small mb-0 mt-1">{{ $option['description'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @error('kondisi_barang')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold required">Catatan Pemeriksaan</label>
                                            <textarea class="form-control @error('catatan_cek') is-invalid @enderror" 
                                                      name="catatan_cek" 
                                                      rows="6" 
                                                      placeholder="Deskripsi detail kondisi alat, bagian yang rusak, atau keterangan lain..."
                                                      required>{{ old('catatan_cek') }}</textarea>
                                            @error('catatan_cek')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Keterangan Tambahan</label>
                                            <textarea class="form-control @error('keterangan_tambahan') is-invalid @enderror" 
                                                      name="keterangan_tambahan" 
                                                      rows="3"
                                                      placeholder="Keterangan tambahan jika diperlukan...">{{ old('keterangan_tambahan') }}</textarea>
                                            @error('keterangan_tambahan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Perhitungan Denda -->
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Perhitungan Denda</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Denda Keterlambatan -->
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Denda Keterlambatan</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Keterlambatan</label>
                                                    <div class="input-group">
                                                        <input type="number" 
                                                               class="form-control @error('hari_terlambat') is-invalid @enderror" 
                                                               name="hari_terlambat" 
                                                               id="hari_terlambat"
                                                               value="{{ old('hari_terlambat', $hari_terlambat ?? 0) }}"
                                                               min="0" 
                                                               readonly>
                                                        <span class="input-group-text">hari</span>
                                                    </div>
                                                    @error('hari_terlambat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Terhitung dari tanggal kembali rencana</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Tarif Denda per Hari</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               value="10000"
                                                               readonly>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Total Denda Keterlambatan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" 
                                                               class="form-control @error('denda_terlambat') is-invalid @enderror" 
                                                               name="denda_terlambat" 
                                                               id="denda_terlambat"
                                                               value="{{ old('denda_terlambat', $denda_terlambat ?? 0) }}"
                                                               readonly>
                                                    </div>
                                                    @error('denda_terlambat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Denda Kerusakan/Hilang -->
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Denda Kerusakan/Hilang</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Jenis Kerusakan</label>
                                                    <div class="form-control-plaintext bg-light p-2 rounded" id="jenis_kerusakan">
                                                        Pilih kondisi barang terlebih dahulu
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Tarif Denda Kerusakan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               id="tarif_kerusakan"
                                                               value="0"
                                                               readonly>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Total Denda Kerusakan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" 
                                                               class="form-control @error('denda_kerusakan') is-invalid @enderror" 
                                                               name="denda_kerusakan" 
                                                               id="denda_kerusakan"
                                                               value="{{ old('denda_kerusakan', $denda_kerusakan ?? 0) }}"
                                                               min="0"
                                                               required>
                                                    </div>
                                                    @error('denda_kerusakan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Dapat disesuaikan sesuai tingkat kerusakan</small>
                                                </div>

                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Perhatian:</strong> Denda kerusakan dapat disesuaikan berdasarkan tingkat kerusakan aktual.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ringkasan Total -->
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Ringkasan Total</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="row mb-2">
                                                    <div class="col-8">
                                                        <strong>Denda Keterlambatan:</strong>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        Rp <span id="display_denda_terlambat">0</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-8">
                                                        <strong>Denda Kerusakan/Hilang:</strong>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        Rp <span id="display_denda_kerusakan">0</span>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row mb-2">
                                                    <div class="col-8">
                                                        <h5 class="mb-0">Total Denda:</h5>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <h5 class="mb-0" id="total_denda_display">Rp 0</h5>
                                                        <input type="hidden" name="total_denda" id="total_denda" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="alert alert-info">
                                                    <small>
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Total denda akan dicatat dalam transaksi dan dapat dibayar secara bertahap.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-warning me-2" onclick="resetForm()">
                                            <i class="fas fa-redo me-2"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.kondisi-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.kondisi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.kondisi-card.selected {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.form-check-input:checked + .card-body {
    background-color: rgba(13, 110, 253, 0.1);
}

.required:after {
    content: " *";
    color: #dc3545;
}

#submitBtn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
</style>
@endpush

@push('scripts')
<script>
// Fungsi untuk menghitung hari terlambat
function calculateLateDays() {
    @if($peminjaman->tanggal_kembali_rencana && $peminjaman->tanggal_kembali)
        const rencana = new Date('{{ $peminjaman->tanggal_kembali_rencana }}');
        const actual = new Date('{{ $peminjaman->tanggal_kembali }}');
        const diffTime = Math.abs(actual - rencana);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    @else
        return 0;
    @endif
}

// Fungsi untuk memilih kondisi
function selectKondisi(kondisi, tarifKerusakan) {
    // Reset semua card
    document.querySelectorAll('.kondisi-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Tandai card yang dipilih
    const selectedCard = document.querySelector(`[data-kondisi="${kondisi}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    // Update tampilan
    const jenisKerusakan = {
        'baik': 'Tidak ada kerusakan',
        'rusak_ringan': 'Kerusakan ringan',
        'rusak_berat': 'Kerusakan berat',
        'hilang': 'Alat hilang'
    }[kondisi];
    
    document.getElementById('jenis_kerusakan').textContent = jenisKerusakan;
    document.getElementById('tarif_kerusakan').value = tarifKerusakan;
    
    // Update denda kerusakan
    document.getElementById('denda_kerusakan').value = tarifKerusakan;
    updateDendaKerusakan();
}

// Fungsi untuk update tampilan denda kerusakan
function updateDendaKerusakan() {
    const dendaKerusakan = parseFloat(document.getElementById('denda_kerusakan').value) || 0;
    document.getElementById('display_denda_kerusakan').textContent = 
        dendaKerusakan.toLocaleString('id-ID');
    calculateTotal();
}

// Fungsi untuk menghitung total denda
function calculateTotal() {
    const dendaTerlambat = parseFloat(document.getElementById('denda_terlambat').value) || 0;
    const dendaKerusakan = parseFloat(document.getElementById('denda_kerusakan').value) || 0;
    const total = dendaTerlambat + dendaKerusakan;
    
    document.getElementById('display_denda_terlambat').textContent = 
        dendaTerlambat.toLocaleString('id-ID');
    document.getElementById('display_denda_kerusakan').textContent = 
        dendaKerusakan.toLocaleString('id-ID');
    document.getElementById('total_denda_display').textContent = 
        'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_denda').value = total;
}

// Fungsi untuk reset form
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang telah diisi akan hilang.')) {
        document.getElementById('transaksiForm').reset();
        document.querySelectorAll('.kondisi-card').forEach(card => {
            card.classList.remove('selected');
        });
        document.getElementById('jenis_kerusakan').textContent = 'Pilih kondisi barang terlebih dahulu';
        document.getElementById('tarif_kerusakan').value = 0;
        document.getElementById('denda_kerusakan').value = 0;
        calculateTotal();
    }
}

// Fungsi untuk validasi form sebelum submit
function validateForm() {
    const kondisi = document.querySelector('input[name="kondisi_barang"]:checked');
    const catatan = document.getElementById('catatan_cek').value.trim();
    const dendaKerusakan = parseFloat(document.getElementById('denda_kerusakan').value) || 0;
    
    if (!kondisi) {
        alert('Silakan pilih kondisi barang terlebih dahulu.');
        return false;
    }
    
    if (!catatan) {
        alert('Silakan isi catatan pemeriksaan.');
        return false;
    }
    
    if (kondisi.value !== 'baik' && dendaKerusakan <= 0) {
        alert('Untuk kondisi ' + kondisi.value + ', mohon isi denda kerusakan yang sesuai.');
        return false;
    }
    
    return true;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Hitung hari terlambat saat halaman dimuat
    const hariTerlambat = calculateLateDays();
    const dendaTerlambat = hariTerlambat * 10000; // Rp 10.000 per hari
    
    document.getElementById('hari_terlambat').value = hariTerlambat;
    document.getElementById('denda_terlambat').value = dendaTerlambat;
    
    // Update tampilan
    calculateTotal();
    
    // Jika ada kondisi yang sudah dipilih sebelumnya
    @if(old('kondisi_barang'))
        const oldKondisi = '{{ old("kondisi_barang") }}';
        const tarifMap = {
            'hilang': 500000,
            'rusak_berat': 300000,
            'rusak_ringan': 100000,
            'baik': 0
        };
        selectKondisi(oldKondisi, tarifMap[oldKondisi] || 0);
    @endif
    
    // Validasi denda kerusakan
    document.getElementById('denda_kerusakan').addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        const kondisi = document.querySelector('input[name="kondisi_barang"]:checked');
        
        if (kondisi && kondisi.value !== 'baik' && value <= 0) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
        
        updateDendaKerusakan();
    });
    
    // Form submission
    document.getElementById('transaksiForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        // Disable submit button
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = 
            '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        return true;
    });
    
    // Validasi realtime
    const catatanField = document.getElementById('catatan_cek');
    catatanField.addEventListener('input', function() {
        if (this.value.trim() === '') {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    
    // Kondisi card click
    document.querySelectorAll('.kondisi-card').forEach(card => {
        card.addEventListener('click', function() {
            const kondisi = this.getAttribute('data-kondisi');
            const tarifMap = {
                'hilang': 500000,
                'rusak_berat': 300000,
                'rusak_ringan': 100000,
                'baik': 0
            };
            selectKondisi(kondisi, tarifMap[kondisi]);
        });
    });
});
</script>
@endpush