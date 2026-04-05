@extends('layouts.app')

@section('title', 'Pengembalian Alat')
@section('header-icon', 'fas fa-undo-alt')
@section('header-title', 'Pengembalian Alat')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-undo me-2"></i>Pengembalian Alat
                </h1>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
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

            @if(session('struk_data'))
                <!-- Modal Struk -->
                <div class="modal fade" id="strukModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-receipt me-2"></i>Bukti Pengembalian
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                @php $struk = session('struk_data'); @endphp
                                
                                <!-- Struk Content -->
                                <div id="strukContent">
                                    <!-- Header -->
                                    <div class="text-center mb-4">
                                        <h4 class="fw-bold mb-1">SISTEM PEMINJAMAN ALAT</h4>
                                        <p class="text-muted mb-0">Bukti Pengembalian Alat</p>
                                        <p class="text-muted">No. {{ $struk['no_struk'] }}</p>
                                    </div>
                                    
                                    <!-- Info Tanggal -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <span class="text-muted">Tanggal Pengembalian</span><br>
                                            <span class="fw-bold">{{ $struk['tanggal'] }}</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="text-muted">Waktu</span><br>
                                            <span class="fw-bold">{{ $struk['waktu'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Garis Pemisah -->
                                    <hr class="my-3" style="border-top: 2px dashed #ccc;">
                                    
                                    <!-- Data Peminjam -->
                                    <div class="mb-3">
                                        <span class="text-muted">Peminjam</span><br>
                                        <span class="fw-bold">{{ $struk['peminjam'] }}</span>
                                    </div>
                                    
                                    <!-- Data Alat -->
                                    <div class="mb-3">
                                        <span class="text-muted">Alat yang Dikembalikan</span><br>
                                        <span class="fw-bold">{{ $struk['alat'] }}</span><br>
                                        <small>Kode: {{ $struk['kode_alat'] }}</small>
                                    </div>
                                    
                                    <!-- Periode Peminjaman -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <span class="text-muted">Tanggal Pinjam</span><br>
                                            <span class="fw-bold">{{ $struk['tanggal_pinjam'] }}</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Rencana Kembali</span><br>
                                            <span class="fw-bold">{{ $struk['tanggal_rencana'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Garis Pemisah -->
                                    <hr class="my-3" style="border-top: 2px dashed #ccc;">
                                    
                                    <!-- Kondisi Alat -->
                                    <div class="mb-3">
                                        <span class="text-muted">Kondisi Alat</span><br>
                                        @php
                                            $kondisiLabel = [
                                                'baik' => 'Baik',
                                                'rusak_ringan' => 'Rusak Ringan',
                                                'rusak_berat' => 'Rusak Berat',
                                                'hilang' => 'Hilang'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $struk['kondisi'] == 'baik' ? 'success' : ($struk['kondisi'] == 'rusak_ringan' ? 'warning' : 'danger') }} p-2">
                                            {{ $kondisiLabel[$struk['kondisi']] ?? $struk['kondisi'] }}
                                        </span>
                                    </div>
                                    
                                    <!-- Keterlambatan & Denda -->
                                    @if($struk['hari_terlambat'] > 0)
                                    <div class="mb-3">
                                        <span class="text-danger">Keterlambatan: {{ $struk['hari_terlambat'] }} hari</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Detail Denda -->
                                    <div class="mb-3">
                                        <table class="table table-sm table-borderless">
                                            @if($struk['denda_keterlambatan'] > 0)
                                            <tr>
                                                <td>Denda Keterlambatan</td>
                                                <td class="text-end">Rp {{ number_format($struk['denda_keterlambatan'], 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            
                                            @if($struk['denda_kerusakan'] > 0)
                                            <tr>
                                                <td>Denda Kerusakan</td>
                                                <td class="text-end">Rp {{ number_format($struk['denda_kerusakan'], 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            
                                            @if($struk['denda_hilang'] > 0)
                                            <tr>
                                                <td>Penggantian Hilang</td>
                                                <td class="text-end">Rp {{ number_format($struk['denda_hilang'], 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            
                                            @if($struk['total_denda'] > 0)
                                            <tr class="fw-bold">
                                                <td>TOTAL DENDA</td>
                                                <td class="text-end text-danger">Rp {{ number_format($struk['total_denda'], 0, ',', '.') }}</td>
                                            </tr>
                                            @if($struk['denda_dibayar'])
                                            <tr>
                                                <td>Status Denda</td>
                                                <td class="text-end"><span class="badge bg-success">Lunas</span></td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td>Status Denda</td>
                                                <td class="text-end"><span class="badge bg-warning">Belum Dibayar</span></td>
                                            </tr>
                                            @endif
                                            @else
                                            <tr>
                                                <td colspan="2" class="text-center text-success">Tidak Ada Denda</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    
                                    <!-- Catatan -->
                                    @if($struk['catatan'])
                                    <div class="mb-3">
                                        <span class="text-muted">Catatan</span><br>
                                        <span class="fst-italic">{{ $struk['catatan'] }}</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Garis Pemisah -->
                                    <hr class="my-3" style="border-top: 2px dashed #ccc;">
                                    
                                    <!-- Footer -->
                                    <div class="text-center">
                                        <p class="mb-1">Terima kasih telah mengembalikan alat tepat waktu</p>
                                        <p class="text-muted small">Struk ini adalah bukti pengembalian yang sah</p>
                                    </div>
                                    
                                    <!-- Tanda Tangan -->
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <p class="text-center mb-0">Peminjam</p>
                                            <div class="text-center mt-4">
                                                <span class="border-top px-4 pt-1">{{ $struk['peminjam'] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-center mb-0">Petugas</p>
                                            <div class="text-center mt-4">
                                                <span class="border-top px-4 pt-1">{{ $struk['petugas'] ?? '_________________' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-primary" id="cetakStrukBtn">
                                    <i class="fas fa-print me-2"></i>Cetak Struk
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">Pilih Alat yang Ingin Dikembalikan</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Klik "Proses Pengembalian" pada alat yang ingin Anda kembalikan.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-primary p-2">
                                <i class="fas fa-clock me-1"></i>
                                {{ $peminjaman->total() }} Peminjaman Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Peminjaman Aktif -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Daftar Peminjaman Aktif</h5>
                </div>
                <div class="card-body">
                    @if($peminjaman->count() > 0)
                        <div class="row">
                            @foreach($peminjaman as $item)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast() ? 'danger' : 'primary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="card-title mb-1">
                                                    <strong>{{ $item->laptop->nama_alat ?? 'Alat' }}</strong>
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $item->laptop->merk ?? '' }} {{ $item->laptop->type ?? '' }}
                                                </small>
                                            </div>
                                            <span class="badge bg-{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast() ? 'danger' : 'success' }}">
                                                {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast() ? 'Terlambat' : 'Aktif' }}
                                            </span>
                                        </div>
                                        
                                        <hr class="my-2">
                                        
                                        <div class="row small">
                                            <div class="col-6">
                                                <span class="text-muted">Kode:</span><br>
                                                <span class="fw-bold">{{ $item->laptop->kode_alat ?? '-' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Serial:</span><br>
                                                <span class="fw-bold">{{ $item->laptop->serial_number ?? '-' }}</span>
                                            </div>
                                        </div>
                                        
                                        <hr class="my-2">
                                        
                                        <div class="row small">
                                            <div class="col-6">
                                                <span class="text-muted">Tanggal Pinjam:</span><br>
                                                <span class="fw-bold">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Rencana Kembali:</span><br>
                                                <span class="fw-bold {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast() ? 'text-danger' : '' }}">
                                                    {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if(\Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast())
                                            <div class="mt-2">
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Terlambat {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->diffInDays(now()) }} hari
                                                </span>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-3">
                                            <a href="{{ route('petugas.pengembalian.proses', $item->id) }}" 
                                               class="btn btn-primary w-100">
                                                <i class="fas fa-undo me-2"></i>Proses Pengembalian
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        @if($peminjaman->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $peminjaman->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-4x text-success"></i>
                            </div>
                            <h5>Tidak Ada Peminjaman Aktif</h5>
                            <p class="text-muted mb-3">Anda tidak memiliki peminjaman yang perlu dikembalikan.</p>
                            <a href="{{ route('user.alat') }}" class="btn btn-primary">
                                <i class="fas fa-laptop me-2"></i>Pinjam Alat
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informasi:</strong>
                                <ul class="mt-2 mb-0">
                                    <li>Pastikan alat dalam kondisi baik sebelum dikembalikan</li>
                                    <li>Denda keterlambatan: Rp 10.000 per hari</li>
                                    <li>Setelah mengajukan, pengembalian akan menunggu konfirmasi petugas</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Perhatian:</strong>
                                <ul class="mt-2 mb-0">
                                    <li>Jika alat rusak/hilang, akan dikenakan denda sesuai ketentuan</li>
                                    <li>Status peminjaman akan berubah setelah dikonfirmasi petugas</li>
                                    <li>Hubungi petugas jika ada kendala</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS untuk Print -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #strukContent, #strukContent * {
        visibility: visible;
    }
    #strukContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px;
        background: white;
    }
    .modal-footer {
        display: none !important;
    }
    .btn-close {
        display: none !important;
    }
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #eef2f7;
}

.badge {
    font-weight: 500;
    padding: 5px 10px;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.card.border-danger {
    border-left: 4px solid #dc3545 !important;
}

.card.border-primary {
    border-left: 4px solid #0d6efd !important;
}

/* Style untuk struk */
#strukContent {
    font-family: 'Courier New', monospace;
    max-width: 400px;
    margin: 0 auto;
}

#strukContent .table {
    font-size: 14px;
}

#strukContent hr {
    border-top: 2px dashed #333;
}

#strukContent .badge {
    font-size: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tampilkan modal struk jika ada data struk
    @if(session('struk_data'))
        var strukModal = new bootstrap.Modal(document.getElementById('strukModal'));
        strukModal.show();
    @endif
    
    // Fungsi cetak struk
    document.getElementById('cetakStrukBtn')?.addEventListener('click', function() {
        window.print();
    });
    
    // Auto close alert
    setTimeout(function() {
        document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection