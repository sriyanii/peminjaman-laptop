@extends('layouts.app')

@section('title', 'Detail Transaksi Denda')
@section('header-icon', 'fas fa-receipt')
@section('header-title', 'Detail Transaksi Denda')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="{{ route('petugas.transaksi.cetak', $transaksi->id) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print me-2"></i>Cetak Struk
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Detail Transaksi Denda #{{ $transaksi->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informasi Transaksi -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">ID Transaksi</small>
                                <p class="mb-0 fw-bold">#{{ $transaksi->id }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Tanggal Transaksi</small>
                                <p class="mb-0 fw-bold">{{ $transaksi->created_at ? $transaksi->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Peminjaman -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-hand-holding me-2"></i>Informasi Peminjaman</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Kode Peminjaman</strong></td>
                                    <td>{{ $transaksi->peminjaman->kode_peminjaman ?? '#' . $transaksi->peminjaman_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Pinjam</strong></td>
                                    <td>{{ $transaksi->peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($transaksi->peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rencana Kembali</strong></td>
                                    <td>{{ $transaksi->peminjaman->tanggal_kembali_rencana ? \Carbon\Carbon::parse($transaksi->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Kembali</strong></td>
                                    <td>{{ $transaksi->peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($transaksi->peminjaman->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi User -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Peminjam</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Nama</strong></td>
                                    <td>{{ $transaksi->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>{{ $transaksi->user->email ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Laptop -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-laptop me-2"></i>Informasi Laptop</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Merk & Model</strong></td>
                                    <td>{{ $transaksi->peminjaman->laptop->merk ?? '-' }} {{ $transaksi->peminjaman->laptop->model ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Serial Number</strong></td>
                                    <td>{{ $transaksi->peminjaman->laptop->serial_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kondisi Saat Kembali</strong></td>
                                    <td>
                                        @php
                                            $kondisiBadges = [
                                                'baik' => 'success',
                                                'rusak_ringan' => 'warning',
                                                'rusak_berat' => 'danger',
                                                'hilang' => 'dark'
                                            ];
                                            $badgeColor = $kondisiBadges[$transaksi->kondisi_barang] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaksi->kondisi_barang)) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Denda -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informasi Denda</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Total Denda</strong></td>
                                    <td class="fw-bold text-danger">Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Denda Dibayar</strong></td>
                                    <td class="fw-bold text-success">Rp {{ number_format($transaksi->denda_dibayar, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sisa Tagihan</strong></td>
                                    <td class="fw-bold text-warning">Rp {{ number_format($transaksi->total_denda - $transaksi->denda_dibayar, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Pembayaran</strong></td>
                                    <td>
                                        @php
                                            $statusBadges = [
                                                'lunas' => 'success',
                                                'belum_lunas' => 'danger',
                                                'sebagian_lunas' => 'warning',
                                                'tidak_ada_denda' => 'info'
                                            ];
                                            $statusColor = $statusBadges[$transaksi->status_pembayaran] ?? 'secondary';
                                            $statusLabels = [
                                                'lunas' => 'LUNAS',
                                                'belum_lunas' => 'BELUM LUNAS',
                                                'sebagian_lunas' => 'SEBAGIAN LUNAS',
                                                'tidak_ada_denda' => 'TIDAK ADA DENDA'
                                            ];
                                            $statusLabel = $statusLabels[$transaksi->status_pembayaran] ?? ucfirst($transaksi->status_pembayaran);
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Metode Pembayaran</strong></td>
                                    <td>{{ ucfirst($transaksi->metode_pembayaran) ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Petugas -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Informasi Petugas</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Petugas Cek</strong></td>
                                    <td>{{ $transaksi->petugas->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Pengecekan</strong></td>
                                    <td>{{ $transaksi->waktu_cek ? \Carbon\Carbon::parse($transaksi->waktu_cek)->format('d/m/Y H:i:s') : '-' }}</td>
                                </tr>
                                @if($transaksi->waktu_pembayaran)
                                <tr>
                                    <td><strong>Waktu Pembayaran</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($transaksi->waktu_pembayaran)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($transaksi->waktu_selesai)
                                <tr>
                                    <td><strong>Waktu Selesai</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($transaksi->waktu_selesai)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Catatan -->
                    @if($transaksi->catatan_cek)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $transaksi->catatan_cek }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Tombol Aksi -->
                    @if(($transaksi->status_pembayaran ?? '') != 'lunas' && ($transaksi->status_pembayaran ?? '') != 'tidak_ada_denda' && ($transaksi->total_denda - $transaksi->denda_dibayar) > 0)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Transaksi ini masih memiliki sisa tagihan.
                        <button type="button" class="btn btn-sm btn-warning ms-3" onclick="bayarDenda({{ $transaksi->id }})">
                            <i class="fas fa-money-bill me-2"></i>Bayar Denda
                        </button>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-center">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Terakhir diupdate: {{ $transaksi->updated_at ? \Carbon\Carbon::parse($transaksi->updated_at)->format('d/m/Y H:i:s') : '-' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bayarDenda(id) {
    window.bayarDenda(id);
}
</script>
@endpush