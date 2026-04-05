@extends('layouts.app')

@section('title', 'Pengembalian Alat')
@section('header-icon', 'fas fa-undo-alt')
@section('header-title', 'Pengembalian Alat')

@section('content')
<div class="container-fluid px-0">
    <!-- Statistik -->
    <div class="row mb-4 g-3">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ $total_pengembalian ?? 0 }}</div>
                <div class="stats-label">Menunggu Konfirmasi</div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number">{{ $terlambat ?? 0 }}</div>
                <div class="stats-label">Terlambat</div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stats-number">{{ $hari_ini ?? 0 }}</div>
                <div class="stats-label">Jatuh Tempo Hari Ini</div>
            </div>
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


    <!-- Tab Content -->
    <div class="tab-content" id="pengembalianTabContent">
        <!-- TAB 1: PEMINJAMAN AKTIF -->
        <div class="tab-pane fade show active" id="peminjaman-aktif" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-list text-primary me-2"></i>
                        Daftar Peminjaman Aktif
                    </h5>

                    <!-- Filter -->
                    <form method="GET" action="{{ route('petugas.pengembalian.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari peminjam / alat" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Peminjam</th>
                                    <th>Alat</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Rencana Kembali</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($peminjaman_aktif as $index => $item)
                                @php
                                    $isTerlambat = \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast();
                                    $namaAlat = $item->laptop ? $item->laptop->merk . ' ' . $item->laptop->model : '-';
                                    $serialNumber = $item->laptop ? $item->laptop->serial_number : '-';
                                @endphp
                                <tr class="{{ $isTerlambat ? 'table-danger' : '' }}">
                                    <td>{{ $peminjaman_aktif->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">#{{ $item->kode_peminjaman ?? 'PJ-'.$item->id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $item->user->name }}</div>
                                        <small class="text-muted">{{ $item->user->email }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $namaAlat }}</div>
                                        <small class="text-muted">{{ $serialNumber }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}
                                        @if($isTerlambat)
                                            <span class="badge bg-danger ms-1">Terlambat</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->status == 'approved' ? 'success' : 'warning' }}">
                                            {{ $item->status == 'approved' ? 'Disetujui' : 'Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                    <button type="button" 
        class="btn btn-sm btn-primary btn-proses-pengembalian"
        data-id="{{ $item->id }}"
        data-route="{{ route('petugas.pengembalian.proses.store', ['id' => $item->id]) }}"
        data-kode="{{ $item->kode_peminjaman ?? 'PJ-'.$item->id }}"
        data-peminjam="{{ $item->user->name }}"
        data-email="{{ $item->user->email }}"
        data-alat="{{ $namaAlat }}"
        data-serial-number="{{ $serialNumber }}"
        data-tgl-pinjam="{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}"
        data-tgl-rencana="{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('Y-m-d') }}"
        data-tgl-rencana-display="{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}"
        data-bs-toggle="modal"
        data-bs-target="#modalProsesPengembalian">
    <i class="fas fa-undo-alt me-1"></i> Proses
</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">Tidak ada peminjaman aktif</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($peminjaman_aktif->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $peminjaman_aktif->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: PENGEMBALIAN PENDING -->
        <div class="tab-pane fade" id="menunggu-konfirmasi" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Pengembalian Menunggu Konfirmasi
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Peminjam</th>
                                    <th>Alat</th>
                                    <th>Tgl Kembali</th>
                                    <th>Kondisi</th>
                                    <th>Estimasi Denda</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengembalian_pending as $index => $item)
                                @php
                                    $kondisi = 'Baik';
                                    if(strpos($item->catatan_pengembalian, 'rusak_ringan') !== false) $kondisi = 'Rusak Ringan';
                                    elseif(strpos($item->catatan_pengembalian, 'rusak_berat') !== false) $kondisi = 'Rusak Berat';
                                    elseif(strpos($item->catatan_pengembalian, 'hilang') !== false) $kondisi = 'Hilang';
                                    
                                    $namaAlat = $item->laptop ? $item->laptop->merk . ' ' . $item->laptop->model : '-';
                                    $serialNumber = $item->laptop ? $item->laptop->serial_number : '-';
                                @endphp
                                <tr>
                                    <td>{{ $pengembalian_pending->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">#{{ $item->kode_peminjaman ?? 'PJ-'.$item->id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $item->user->name }}</div>
                                        <small class="text-muted">{{ $item->user->email }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $namaAlat }}</div>
                                        <small class="text-muted">{{ $serialNumber }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $kondisi == 'Baik' ? 'success' : ($kondisi == 'Hilang' ? 'danger' : 'warning') }}">
                                            {{ $kondisi }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->denda > 0)
                                            <span class="badge bg-warning">
                                                Rp {{ number_format($item->denda, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">Tidak Ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button"
        class="btn btn-sm btn-warning btn-konfirmasi-pending"
        data-id="{{ $item->id }}"
        data-route="{{ route('petugas.pengembalian.konfirmasi.store', $item->id) }}"  
        data-kode="{{ $item->kode_peminjaman ?? 'PJ-'.$item->id }}"
        data-peminjam="{{ $item->user->name }}"
        data-denda="{{ $item->denda }}"
        data-kondisi="{{ $kondisi }}"
        data-bs-toggle="modal"
        data-bs-target="#modalKonfirmasiPending">
    <i class="fas fa-check-circle me-1"></i> Konfirmasi
</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                            <p class="mb-0">Tidak ada pengembalian yang perlu dikonfirmasi</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($pengembalian_pending->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pengembalian_pending->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PROSES PENGEMBALIAN -->
<div class="modal fade" id="modalProsesPengembalian" tabindex="-1" aria-labelledby="modalProsesPengembalianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProsesPengembalianLabel">
                    <i class="fas fa-undo-alt text-primary me-2"></i>Proses Pengembalian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="formProsesPengembalian">
                @csrf
                <div class="modal-body">
                    <!-- Informasi Peminjaman -->
                    <div class="alert alert-info mb-3" id="infoPeminjaman">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Kode:</strong> <span id="infoKode"></span><br>
                                <strong>Peminjam:</strong> <span id="infoPeminjam"></span><br>
                                <strong>Email:</strong> <span id="infoEmail"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Alat:</strong> <span id="infoAlat"></span><br>
                                <strong>Serial Number:</strong> <span id="infoSerialNumber"></span><br>
                                <strong>Tgl Pinjam:</strong> <span id="infoTglPinjam"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="tanggal_kembali" 
                                   class="form-control @error('tanggal_kembali') is-invalid @enderror" 
                                   value="{{ date('Y-m-d') }}" 
                                   max="{{ date('Y-m-d') }}" 
                                   required 
                                   id="modal_tanggal_kembali">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-select" required id="modal_kondisi">
                                <option value="">Pilih Kondisi</option>
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>
                    </div>

                    <!-- Detail Denda -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Detail Denda</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label>Denda Terlambat (Rp 10.000/hari)</label>
                                    <input type="text" class="form-control bg-white" id="modal_denda_terlambat_display" readonly value="Rp 0">
                                    <input type="hidden" name="denda_terlambat" id="modal_denda_terlambat" value="0">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Denda Kerusakan</label>
                                    <input type="text" class="form-control bg-white" id="modal_denda_kerusakan_display" readonly value="Rp 0">
                                    <input type="hidden" name="denda_kerusakan" id="modal_denda_kerusakan" value="0">
                                    <small class="text-muted">Ringan: Rp50k, Berat: Rp200k</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Denda Hilang</label>
                                    <input type="text" class="form-control bg-white" id="modal_denda_hilang_display" readonly value="Rp 0">
                                    <input type="hidden" name="denda_hilang" id="modal_denda_hilang" value="0">
                                    <small class="text-muted">Rp1.000.000</small>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3 mb-0 d-flex justify-content-between align-items-center">
                                <strong>Total Denda:</strong>
                                <span class="fw-bold fs-5 text-primary" id="modal_total_denda_display">Rp 0</span>
                                <input type="hidden" name="total_denda" id="modal_total_denda" value="0">
                            </div>
                            
                            <div id="modal_warningMessage" class="mt-2" style="display: none;">
                                <div class="alert alert-warning py-2 mb-0">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <span id="modal_warningText"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pengecekan..." id="modal_catatan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="modalBtnSubmit">
                        <i class="fas fa-save"></i> Proses Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI PENDING -->
<!-- MODAL KONFIRMASI PENDING -->
<div class="modal fade" id="modalKonfirmasiPending" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-warning me-2"></i>Konfirmasi Pengembalian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- PERBAIKAN: ACTION KOSONG, AKAN DIISI JAVASCRIPT --}}
            <form method="POST" action="" id="formKonfirmasiPending">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Kode:</strong> <span id="pendingKode"></span><br>
                        <strong>Peminjam:</strong> <span id="pendingPeminjam"></span><br>
                        <strong>Estimasi Denda:</strong> <span id="pendingDenda"></span><br>
                        <strong>Kondisi:</strong> <span id="pendingKondisi"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Konfirmasi <span class="text-danger">*</span></label>
                        <select name="status_konfirmasi" class="form-select" required id="status_konfirmasi">
                            <option value="">Pilih Status</option>
                            <option value="setuju">Setujui</option>
                            <option value="tolak">Tolak</option>
                        </select>
                    </div>

                    <div class="mb-3" id="dendaFinalField" style="display: none;">
                        <label class="form-label">Denda Final</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="denda_final" class="form-control" id="denda_final" min="0" step="1000">
                        </div>
                        <small class="text-muted">Kosongkan jika menggunakan estimasi denda</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Petugas</label>
                        <textarea name="catatan_petugas" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check-circle"></i> Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.stats-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    height: 100%;
    transition: transform 0.3s ease;
    border: none;
}
.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 20px;
}
.stats-number {
    font-size: 32px;
    font-weight: 700;
    margin: 10px 0;
    color: var(--dark);
}
.stats-label {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 10px;
}
.table-danger {
    background-color: #fff2f0;
}
.table-danger:hover {
    background-color: #ffe6e0 !important;
}
.nav-tabs .nav-link {
    font-weight: 500;
    color: #495057;
    border: none;
    padding: 12px 20px;
}
.nav-tabs .nav-link.active {
    color: #0d6efd;
    background-color: transparent;
    border-bottom: 3px solid #0d6efd;
}
.nav-tabs .nav-link:hover {
    border-bottom: 3px solid #dee2e6;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Konstanta denda
    const DENDA_PER_HARI = 10000;
    const DENDA_RUSAK_RINGAN = 50000;
    const DENDA_RUSAK_BERAT = 200000;
    const DENDA_HILANG = 1000000;
    
    // Modal Proses Pengembalian
    const modalProses = document.getElementById('modalProsesPengembalian');
    const btnProses = document.querySelectorAll('.btn-proses-pengembalian');
    const formProses = document.getElementById('formProsesPengembalian');
    
    // Modal Konfirmasi Pending
    const modalKonfirmasi = document.getElementById('modalKonfirmasiPending');
    const btnKonfirmasi = document.querySelectorAll('.btn-konfirmasi-pending');
    const formKonfirmasi = document.getElementById('formKonfirmasiPending');
    
    // Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Hitung selisih hari
    function hitungSelisihHari(tgl1, tgl2) {
        const date1 = new Date(tgl1.toDateString());
        const date2 = new Date(tgl2.toDateString());
        const diffTime = Math.abs(date2 - date1);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
    
    // Hitung denda di modal
    function hitungDendaModal() {
        const tglKembaliInput = document.getElementById('modal_tanggal_kembali');
        const kondisiSelect = document.getElementById('modal_kondisi');
        const tglRencanaInput = document.getElementById('modal_tgl_rencana');
        
        if (!tglKembaliInput || !kondisiSelect || !tglRencanaInput) return;
        if (!tglKembaliInput.value) return;
        
        const tglKembali = new Date(tglKembaliInput.value + 'T00:00:00');
        const tglRencana = new Date(tglRencanaInput.value + 'T00:00:00');
        
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
        document.getElementById('modal_denda_terlambat_display').value = formatRupiah(dendaTerlambat);
        document.getElementById('modal_denda_terlambat').value = dendaTerlambat;
        
        document.getElementById('modal_denda_kerusakan_display').value = formatRupiah(dendaKerusakan);
        document.getElementById('modal_denda_kerusakan').value = dendaKerusakan;
        
        document.getElementById('modal_denda_hilang_display').value = formatRupiah(dendaHilang);
        document.getElementById('modal_denda_hilang').value = dendaHilang;
        
        document.getElementById('modal_total_denda_display').innerText = formatRupiah(total);
        document.getElementById('modal_total_denda').value = total;
        
        // Tampilkan warning
        const warningDiv = document.getElementById('modal_warningMessage');
        const warningText = document.getElementById('modal_warningText');
        
        if (kondisi === 'hilang') {
            warningText.innerText = 'Barang hilang akan dikenakan denda Rp 1.000.000 dan status barang akan diubah menjadi hilang.';
            warningDiv.style.display = 'block';
        } else if (dendaTerlambat > 0) {
            warningText.innerText = `Terlambat ${diffDays} hari dengan denda ${formatRupiah(dendaTerlambat)}.`;
            warningDiv.style.display = 'block';
        } else {
            warningDiv.style.display = 'none';
        }
    }
    
// Event listener untuk tombol proses
btnProses.forEach(btn => {
    btn.addEventListener('click', function() {
        // Ambil data dari atribut tombol
        const route = this.dataset.route; // <-- INI YANG PALING PENTING
        const id = this.dataset.id;
        const kode = this.dataset.kode;
        const peminjam = this.dataset.peminjam;
        const email = this.dataset.email;
        const alat = this.dataset.alat;
        const serialNumber = this.dataset.serialNumber;
        const tglPinjam = this.dataset.tglPinjam;
        const tglRencana = this.dataset.tglRencana;
        
        // CEK DI CONSOLE BROWSER
        console.log('=== DEBUG MODAL ===');
        console.log('ID:', id);
        console.log('Route dari data-route:', route);
        console.log('Form element:', formProses);
        
        // SET ACTION FORM DENGAN ROUTE YANG BENAR
        if (route) {
            formProses.action = route;
            console.log('Action form di-set ke:', formProses.action);
        } else {
            console.error('ROUTE TIDAK DITEMUKAN! data-route kosong');
            alert('Error: Route tidak ditemukan. Cek atribut data-route pada tombol.');
            return;
        }
        
        // Set info peminjaman
        document.getElementById('infoKode').innerText = kode;
        document.getElementById('infoPeminjam').innerText = peminjam;
        document.getElementById('infoEmail').innerText = email;
        document.getElementById('infoAlat').innerText = alat;
        document.getElementById('infoSerialNumber').innerText = serialNumber;
        document.getElementById('infoTglPinjam').innerText = tglPinjam;
        
        // Set hidden field untuk tanggal rencana
        let hiddenField = document.getElementById('modal_tgl_rencana');
        if (!hiddenField) {
            hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.id = 'modal_tgl_rencana';
            hiddenField.name = 'tgl_rencana';
            formProses.appendChild(hiddenField);
            console.log('Hidden field dibuat');
        }
        hiddenField.value = tglRencana;
        console.log('Tanggal rencana:', tglRencana);
        
        // Reset form
        document.getElementById('modal_tanggal_kembali').value = new Date().toISOString().split('T')[0];
        document.getElementById('modal_kondisi').value = '';
        document.getElementById('modal_catatan').value = '';
        
        // Reset display denda
        document.getElementById('modal_denda_terlambat_display').value = 'Rp 0';
        document.getElementById('modal_denda_kerusakan_display').value = 'Rp 0';
        document.getElementById('modal_denda_hilang_display').value = 'Rp 0';
        document.getElementById('modal_total_denda_display').innerText = 'Rp 0';
        document.getElementById('modal_total_denda').value = '0';
        
        // Hitung denda awal
        setTimeout(hitungDendaModal, 100);
    });
});
    
    // Event listener untuk input di modal
    const tglKembaliInput = document.getElementById('modal_tanggal_kembali');
    const kondisiSelect = document.getElementById('modal_kondisi');
    
    if (tglKembaliInput) {
        tglKembaliInput.addEventListener('change', hitungDendaModal);
    }
    
    if (kondisiSelect) {
        kondisiSelect.addEventListener('change', hitungDendaModal);
    }
    
    // Event listener untuk tombol konfirmasi pending
// Event listener untuk tombol konfirmasi pending
btnKonfirmasi.forEach(btn => {
    btn.addEventListener('click', function() {
        const route = this.dataset.route; // AMBIL DARI DATA ATTRIBUTE
        const id = this.dataset.id;
        const kode = this.dataset.kode;
        const peminjam = this.dataset.peminjam;
        const denda = this.dataset.denda;
        const kondisi = this.dataset.kondisi;
        
        // SET ACTION FORM
        formKonfirmasi.action = route;
        
        document.getElementById('pendingKode').innerText = kode;
        document.getElementById('pendingPeminjam').innerText = peminjam;
        document.getElementById('pendingDenda').innerText = formatRupiah(parseInt(denda));
        document.getElementById('pendingKondisi').innerText = kondisi;
        
        // Reset form
        document.getElementById('status_konfirmasi').value = '';
        document.getElementById('denda_final').value = '';
        document.querySelector('#formKonfirmasiPending textarea[name="catatan_petugas"]').value = '';
        document.getElementById('dendaFinalField').style.display = 'none';
    });
});  
    // Toggle field denda final berdasarkan status konfirmasi
    const statusKonfirmasi = document.getElementById('status_konfirmasi');
    if (statusKonfirmasi) {
        statusKonfirmasi.addEventListener('change', function() {
            const dendaFinalField = document.getElementById('dendaFinalField');
            if (this.value === 'setuju') {
                dendaFinalField.style.display = 'block';
            } else {
                dendaFinalField.style.display = 'none';
            }
        });
    }
    
    // Validasi form sebelum submit
    if (formProses) {
        formProses.addEventListener('submit', function(e) {
            const tanggal = document.getElementById('modal_tanggal_kembali').value;
            const kondisi = document.getElementById('modal_kondisi').value;
            const totalDenda = parseInt(document.getElementById('modal_total_denda').value) || 0;
            
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
            const btnSubmit = document.getElementById('modalBtnSubmit');
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            btnSubmit.disabled = true;
            
            return true;
        });
    }
});
</script>
@endpush