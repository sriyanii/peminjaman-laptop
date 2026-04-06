@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('header-icon', 'fas fa-info-circle')
@section('header-title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-laptop text-primary me-2"></i>
                            Detail Peminjaman #{{ $peminjaman->id }}
                        </h5>
                        <div>
                            <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-user me-2"></i>Informasi Peminjam</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td width="35%">Nama</td><td><strong>{{ $peminjaman->user->name ?? '-' }}</strong></td></tr>
                                        <tr><td>Email</td><td>{{ $peminjaman->user->email ?? '-' }}</td></tr>
                                        <tr><td>Telepon</td><td>{{ $peminjaman->user->phone ?? '-' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-laptop me-2"></i>Informasi Laptop</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td width="35%">Merk & Model</td><td><strong>{{ $peminjaman->laptop->merk ?? '' }} {{ $peminjaman->laptop->model ?? '-' }}</strong></td></tr>
                                        <tr><td>Serial Number</td><td>{{ $peminjaman->laptop->serial_number ?? '-' }}</td></tr>
                                        <tr><td>Harga Sewa/Hari</td><td>Rp {{ number_format($peminjaman->laptop->harga_sewa_harian ?? 50000, 0, ',', '.') }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-calendar me-2"></i>Informasi Peminjaman</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td width="40%">Tanggal Pinjam</td><td><strong>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</strong></td></tr>
                                        <tr><td>Rencana Kembali</td><td><strong>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</strong></td></tr>
                                        <tr><td>Tanggal Kembali</td><td>{{ $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') : '-' }}</td></tr>
                                        <tr><td>Lama Hari</td><td>{{ $peminjaman->lama_hari }} hari</td></tr>
                                        <tr><td>Harga Sewa</td><td>Rp {{ number_format($peminjaman->harga_sewa, 0, ',', '.') }}</td></tr>
                                        <tr><td>Denda</td><td>Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</td></tr>
                                        <tr><td>Total Tagihan</td><td>Rp {{ number_format($peminjaman->total_tagihan ?? $peminjaman->harga_sewa, 0, ',', '.') }}</td></tr>
                                        <tr><td>Tujuan</td><td>{{ ucfirst($peminjaman->tujuan) }}</td></tr>
                                        <tr><td>Keterangan</td><td>{{ $peminjaman->keterangan ?? '-' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-info-circle me-2"></i>Informasi Lainnya</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="20%">Status</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'approved' => 'info',
                                                        'aktif' => 'primary',
                                                        'selesai' => 'success',
                                                        'ditolak' => 'danger',
                                                        'batal' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$peminjaman->status] ?? 'secondary' }}">
                                                    {{ ucfirst($peminjaman->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($peminjaman->alasan_ditolak)
                                        <tr><td>Alasan Ditolak</td><td>{{ $peminjaman->alasan_ditolak }}</td></tr>
                                        @endif
                                        @if($peminjaman->catatan_pengembalian)
                                        <tr><td>Catatan Pengembalian</td><td>{{ $peminjaman->catatan_pengembalian }}</td></tr>
                                        @endif
                                        <tr><td>Dibuat Pada</td><td>{{ \Carbon\Carbon::parse($peminjaman->created_at)->format('d/m/Y H:i:s') }}</td></tr>
                                        <tr><td>Terakhir Update</td><td>{{ \Carbon\Carbon::parse($peminjaman->updated_at)->format('d/m/Y H:i:s') }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection