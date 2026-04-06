@extends('layouts.app')

@section('title', 'Detail Transaksi Denda')
@section('header-title', 'Detail Transaksi Denda')
@section('header-icon', 'fas fa-receipt')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Transaksi
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Detail Transaksi Denda #{{ $transaction->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informasi Transaksi -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">ID Transaksi</small>
                                <p class="mb-0 fw-bold">#{{ $transaction->id }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Tanggal Transaksi</small>
                                <p class="mb-0 fw-bold">{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i:s') : '-' }}</p>
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
                                    <td>{{ $transaction->peminjaman->kode_peminjaman ?? '#' . $transaction->peminjaman_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Pinjam</strong></td>
                                    <td>{{ $transaction->peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($transaction->peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rencana Kembali</strong></td>
                                    <td>{{ $transaction->peminjaman->tanggal_kembali_rencana ? \Carbon\Carbon::parse($transaction->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Kembali</strong></td>
                                    <td>{{ $transaction->peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($transaction->peminjaman->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
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
                                    <td>{{ $transaction->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>{{ $transaction->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. Telepon</strong></td>
                                    <td>{{ $transaction->user->phone ?? '-' }}</td>
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
                                    <td>{{ $transaction->peminjaman->laptop->merk ?? '-' }} {{ $transaction->peminjaman->laptop->model ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Serial Number</strong></td>
                                    <td>{{ $transaction->peminjaman->laptop->serial_number ?? '-' }}</td>
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
                                            $badgeColor = $kondisiBadges[$transaction->kondisi_barang] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->kondisi_barang)) }}
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
                                    <td class="fw-bold text-danger">Rp {{ number_format($transaction->total_denda, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Denda Dibayar</strong></td>
                                    <td class="fw-bold text-success">Rp {{ number_format($transaction->denda_dibayar, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sisa Tagihan</strong></td>
                                    <td class="fw-bold text-warning">Rp {{ number_format($transaction->total_denda - $transaction->denda_dibayar, 0, ',', '.') }}</td>
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
                                            $statusColor = $statusBadges[$transaction->status_pembayaran] ?? 'secondary';
                                            $statusLabels = [
                                                'lunas' => 'LUNAS',
                                                'belum_lunas' => 'BELUM LUNAS',
                                                'sebagian_lunas' => 'SEBAGIAN LUNAS',
                                                'tidak_ada_denda' => 'TIDAK ADA DENDA'
                                            ];
                                            $statusLabel = $statusLabels[$transaction->status_pembayaran] ?? ucfirst($transaction->status_pembayaran);
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Metode Pembayaran</strong></td>
                                    <td>{{ ucfirst($transaction->metode_pembayaran) ?? '-' }}</td>
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
                                    <td>{{ $transaction->petugas->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Pengecekan</strong></td>
                                    <td>{{ $transaction->waktu_cek ? \Carbon\Carbon::parse($transaction->waktu_cek)->format('d/m/Y H:i:s') : '-' }}</td>
                                </tr>
                                @if($transaction->waktu_pembayaran)
                                <tr>
                                    <td><strong>Waktu Pembayaran</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->waktu_pembayaran)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($transaction->waktu_selesai)
                                <tr>
                                    <td><strong>Waktu Selesai</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->waktu_selesai)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Catatan -->
                    @if($transaction->catatan_cek)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $transaction->catatan_cek }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Tombol Aksi -->
                    @if(($transaction->status_pembayaran ?? '') != 'lunas' && ($transaction->status_pembayaran ?? '') != 'tidak_ada_denda' && ($transaction->total_denda - $transaction->denda_dibayar) > 0)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Transaksi ini masih memiliki sisa tagihan.
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-center">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Terakhir diupdate: {{ $transaction->updated_at ? \Carbon\Carbon::parse($transaction->updated_at)->format('d/m/Y H:i:s') : '-' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Tambahkan JavaScript jika diperlukan
</script>
@endpush