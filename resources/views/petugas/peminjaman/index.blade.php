{{-- resources/views/petugas/peminjaman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Peminjaman')
@section('header-icon', 'fas fa-hand-holding')
@section('header-title', 'Manajemen Peminjaman')

@section('content')
<div class="container-fluid px-0">
    <!-- STATISTIK -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['total'] ?? 0) }}</div>
                <div class="stats-label">Total Peminjaman</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['pending'] ?? 0) }}</div>
                <div class="stats-label">Menunggu</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['active'] ?? 0) }}</div>
                <div class="stats-label">Sedang Dipinjam</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ number_format($statistics['returned'] ?? 0) }}</div>
                <div class="stats-label">Selesai</div>
            </div>
        </div>
    </div>

    <!-- Alert Session -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- TABEL PEMINJAMAN -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Daftar Peminjaman
                </h5>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak
                </button>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <form method="GET" action="{{ route('petugas.peminjaman.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pencarian</label>
                            <input type="text" class="form-control" name="search" placeholder="Kode, peminjam, atau laptop" value="{{ request('search') }}">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filter</button>
                            <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th><th>Kode</th><th>Peminjam</th><th>Laptop</th>
                            <th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Status</th>
                            <th class="text-center" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjaman as $item)
                        @php
                            $statusColors = ['pending'=>'warning','approved'=>'info','aktif'=>'primary','selesai'=>'success','ditolak'=>'danger','batal'=>'secondary'];
                            $statusLabels = ['pending'=>'Menunggu','approved'=>'Disetujui','aktif'=>'Dipinjam','selesai'=>'Selesai','ditolak'=>'Ditolak','batal'=>'Batal'];
                            $kodePeminjaman = $item->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($item->created_at)) . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td><span class="badge bg-light text-dark">#{{ $item->id }}</span></td>
                            <td><span class="fw-bold">{{ $kodePeminjaman }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $item->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $item->laptop->merk ?? '-' }} {{ $item->laptop->model ?? '' }}</div>
                                <small class="text-muted">SN: {{ $item->laptop->serial_number ?? '-' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d/m/Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->translatedFormat('d/m/Y') }}
                                @if(in_array($item->status, ['approved', 'aktif']) && \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast())
                                    <span class="badge bg-danger ms-1">Terlambat!</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$item->status] ?? ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('petugas.peminjaman.show', $item->id) }}" class="btn btn-outline-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($item->status == 'pending')
                                        <button type="button" class="btn btn-outline-success" title="Setujui" onclick="confirmApprove({{ $item->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Tolak" onclick="confirmReject({{ $item->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    @if($item->status == 'approved')
                                        <button type="button" class="btn btn-outline-primary" title="Konfirmasi Pengambilan" onclick="confirmPickup({{ $item->id }})">
                                            <i class="fas fa-box-open"></i>
                                        </button>
                                    @endif
                                    
@if(in_array($item->status, ['approved', 'aktif']))
    <button type="button" class="btn btn-warning" title="Proses Pengembalian" 
            onclick="bukaTransaksi({{ $item->id }}, 
                    '{{ addslashes($item->user->name) }}', 
                    '{{ addslashes($item->laptop->merk) }} {{ addslashes($item->laptop->model) }}', 
                    '{{ $item->laptop->serial_number }}', 
                    '{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}', 
                    '{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}', 
                    {{ $item->laptop->harga_sewa_harian ?? 0 }})">
        <i class="fas fa-exchange-alt"></i> Kembali
    </button>
@endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-hand-holding fa-3x mb-3 d-block"></i>
                            <h5 class="mb-1">Belum ada peminjaman</h5>
                            <p class="mb-0">Data peminjaman akan muncul di sini setelah ada transaksi</p>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="d-flex justify-content-center mt-4">
                {{ $peminjaman->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL TRANSAKSI ===== -->
<div class="modal fade" id="modalTransaksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTransaksi">
                @csrf
                <input type="hidden" name="peminjaman_id" id="peminjaman_id">
                <input type="hidden" name="harga_sewa_harian" id="harga_sewa_harian">
                
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exchange-alt me-2"></i> Form Pengembalian & Denda
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Info Peminjaman -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Peminjam:</strong> <span id="infoUser"></span><br>
                                <strong>Laptop:</strong> <span id="infoLaptop"></span><br>
                                <strong>Serial:</strong> <span id="infoSerial"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tgl Pinjam:</strong> <span id="infoPinjam"></span><br>
                                <strong>Rencana Kembali:</strong> <span id="infoRencana"></span><br>
                                <strong>Harga Sewa/Hari:</strong> Rp <span id="infoHarga">0</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kembali" id="tanggal_kembali" 
                                   class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Lama Peminjaman</label>
                            <input type="text" class="form-control" id="lama_hari_display" readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Kondisi Barang <span class="text-danger">*</span></label>
                            <select name="kondisi" id="kondisi_barang" class="form-select" required>
                                <option value="baik">✅ Baik (Normal)</option>
                                <option value="rusak_ringan">⚠️ Rusak Ringan (Denda 50% dari harga sewa)</option>
                                <option value="rusak_berat">❌ Rusak Berat (Denda 100% dari harga sewa)</option>
                                <option value="hilang">🔴 HILANG (Ganti Rugi - 10x harga sewa)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Total Biaya Sewa</label>
                            <input type="text" class="form-control" id="total_biaya_sewa_display" readonly>
                        </div>
                        
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">💰 Rincian Denda</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small>Hari Terlambat:</small>
                                            <div class="fw-bold" id="hari_terlambat_display">0 hari</div>
                                        </div>
                                        <div class="col-md-4">
                                            <small>Denda Telat:</small>
                                            <div class="fw-bold text-danger" id="dendaTelatDisplay">Rp 0</div>
                                        </div>
                                        <div class="col-md-4">
                                            <small>Denda per Hari:</small>
                                            <div class="fw-bold">Rp 10.000</div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small>Denda Kerusakan:</small>
                                            <div class="fw-bold text-danger" id="dendaRusakDisplay">Rp 0</div>
                                        </div>
                                        <div class="col-md-6">
                                            <small>Persentase Denda:</small>
                                            <div class="fw-bold" id="persen_denda_display">0%</div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small><strong>Total Denda:</strong></small>
                                            <div class="fw-bold text-danger fs-5" id="totalDendaDisplay">Rp 0</div>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Total Tagihan:</strong></small>
                                            <div class="fw-bold text-danger fs-5" id="totalTagihanDisplay">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Metode Bayar</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select">
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Jumlah Dibayar</label>
                            <input type="number" name="jumlah_bayar" id="jumlah_bayar" 
                                   class="form-control" value="0" min="0" step="1000">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Sisa Tagihan</label>
                            <div class="form-control-plaintext fw-bold text-danger fs-5" id="sisaTagihan">Rp 0</div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i> Proses Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL REJECT ===== -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Tolak Peminjaman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                @csrf
                <input type="hidden" id="reject_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_ditolak" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i> Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});

var currentHargaSewaHarian = 0;
var currentLamaHari = 0;
var currentTotalSewa = 0;

// Buka modal transaksi
window.bukaTransaksi = function(id, user, laptop, serial, pinjam, rencana, hargaSewaHarian) {
    currentHargaSewaHarian = parseFloat(hargaSewaHarian) || 0;
    
    // Hitung lama peminjaman dalam hari
    let tglPinjam = parseDate(pinjam);
    let tglRencana = parseDate(rencana);
    currentLamaHari = Math.ceil((tglRencana - tglPinjam) / (1000 * 60 * 60 * 24)) || 1;
    currentTotalSewa = currentHargaSewaHarian * currentLamaHari;
    
    $('#peminjaman_id').val(id);
    $('#harga_sewa_harian').val(currentHargaSewaHarian);
    $('#infoUser').text(user);
    $('#infoLaptop').text(laptop);
    $('#infoSerial').text(serial);
    $('#infoPinjam').text(pinjam);
    $('#infoRencana').text(rencana);
    $('#infoHarga').text(currentHargaSewaHarian.toLocaleString('id-ID'));
    $('#lama_hari_display').val(currentLamaHari + ' hari');
    $('#total_biaya_sewa_display').val('Rp ' + currentTotalSewa.toLocaleString('id-ID'));
    
    $('#tanggal_kembali').val(new Date().toISOString().split('T')[0]);
    $('#kondisi_barang').val('baik');
    $('#jumlah_bayar').val(0);
    $('#catatan').val('');
    
    hitungDenda();
    $('#modalTransaksi').modal('show');
};

// Helper function untuk parsing date
function parseDate(dateStr) {
    if (dateStr.includes('/')) {
        let parts = dateStr.split('/');
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }
    return new Date(dateStr);
}

// Hitung denda
function hitungDenda() {
    const hargaHarian = currentHargaSewaHarian;
    const lamaSewa = currentLamaHari;
    const totalSewa = currentTotalSewa;
    
    // Parse tanggal
    const rencanaStr = $('#infoRencana').text();
    const tanggalKembali = new Date($('#tanggal_kembali').val());
    let rencana = parseDate(rencanaStr);
    
    // Hitung keterlambatan
    let hariTerlambat = 0;
    let dendaTelat = 0;
    if (tanggalKembali > rencana) {
        hariTerlambat = Math.ceil((tanggalKembali - rencana) / (1000 * 60 * 60 * 24));
        dendaTelat = hariTerlambat * 10000; // Rp 10.000/hari
    }
    
    // Hitung denda kerusakan berdasarkan kondisi
    const kondisi = $('#kondisi_barang').val();
    let dendaRusak = 0;
    let persenDenda = 0;
    
    switch (kondisi) {
        case 'rusak_ringan':
            dendaRusak = totalSewa * 0.5;
            persenDenda = 50;
            break;
        case 'rusak_berat':
            dendaRusak = totalSewa;
            persenDenda = 100;
            break;
        case 'hilang':
            dendaRusak = totalSewa * 10;
            persenDenda = 1000;
            break;
        case 'baik':
        default:
            dendaRusak = 0;
            persenDenda = 0;
            break;
    }
    
    let totalDenda = dendaTelat + dendaRusak;
    let totalTagihan = totalSewa + totalDenda;
    let jumlahBayar = parseFloat($('#jumlah_bayar').val()) || 0;
    let sisa = Math.max(0, totalTagihan - jumlahBayar);
    
    // Update display
    $('#hari_terlambat_display').text(hariTerlambat + ' hari');
    $('#dendaTelatDisplay').text('Rp ' + dendaTelat.toLocaleString('id-ID'));
    $('#dendaRusakDisplay').text('Rp ' + dendaRusak.toLocaleString('id-ID'));
    $('#persen_denda_display').text(persenDenda + '%');
    $('#totalDendaDisplay').text('Rp ' + totalDenda.toLocaleString('id-ID'));
    $('#totalTagihanDisplay').text('Rp ' + totalTagihan.toLocaleString('id-ID'));
    $('#sisaTagihan').text('Rp ' + sisa.toLocaleString('id-ID'));
}

// Event listeners
$('#tanggal_kembali, #kondisi_barang, #jumlah_bayar').on('change input', hitungDenda);

// Submit form transaksi
$('#formTransaksi').on('submit', function(e) {
    e.preventDefault();
    
    let totalTagihanText = $('#totalTagihanDisplay').text();
    let totalTagihan = parseFloat(totalTagihanText.replace(/[^0-9,-]/g, '').replace(',', '')) || 0;
    let jumlahBayar = parseFloat($('#jumlah_bayar').val()) || 0;
    
    if (totalTagihan > 0 && jumlahBayar === 0) {
        Swal.fire({
            title: 'Perhatian!',
            text: 'Denda belum dibayar. Lanjutkan proses pengembalian?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesPengembalian();
            }
        });
    } else {
        prosesPengembalian();
    }
});

function prosesPengembalian() {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Proses pengembalian laptop?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            let formData = $('#formTransaksi').serialize();
            
            $.ajax({
                url: '/petugas/peminjaman/' + $('#peminjaman_id').val() + '/transaksi',
                type: 'POST',
                data: formData,
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', message, 'error');
                }
            });
        }
    });
}

// Approve, Reject, ConfirmPickup functions remain the same...
window.confirmApprove = function(id) {
    Swal.fire({
        title: 'Setujui Peminjaman?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            $.ajax({
                url: '/petugas/peminjaman/' + id + '/approve',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
};

window.confirmReject = function(id) {
    Swal.fire({
        title: 'Alasan Penolakan',
        input: 'textarea',
        inputLabel: 'Masukkan alasan penolakan',
        inputPlaceholder: 'Tulis alasan penolakan di sini...',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan penolakan harus diisi!';
            }
            if (value.length < 5) {
                return 'Alasan minimal 5 karakter';
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            $.ajax({
                url: '/petugas/peminjaman/' + id + '/reject',
                type: 'POST',
                data: { alasan_ditolak: result.value },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
};

window.confirmPickup = function(id) {
    Swal.fire({
        title: 'Konfirmasi Pengambilan',
        text: 'Peminjam sudah mengambil laptop?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Sudah!',
        cancelButtonText: 'Belum'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            $.ajax({
                url: '/petugas/peminjaman/' + id + '/pickup',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
};
</script>
@endpush