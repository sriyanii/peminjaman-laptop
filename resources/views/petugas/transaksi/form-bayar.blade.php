{{-- resources/views/petugas/transaksi/form-bayar.blade.php --}}
@extends('layouts.app')

@section('title', 'Form Pembayaran Denda')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill me-2"></i> Form Pembayaran Denda
                    </h5>
                </div>
                
                <div class="card-body">
                    <!-- Informasi Transaksi -->
                    <div class="alert alert-info">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="40%">ID Transaksi</td>
                                <td>: <strong>#{{ $transaksi->id }}</strong>
                            </tr>
                            <tr>
                                <td>Peminjam</td>
                                <td>: <strong>{{ $transaksi->user->name ?? '-' }}</strong>
                            </tr>
                            <tr>
                                <td>Laptop</td>
                                <td>: <strong>{{ $transaksi->peminjaman->laptop->merk ?? '-' }} {{ $transaksi->peminjaman->laptop->model ?? '' }}</strong>
                            </tr>
                            <tr>
                                <td>Total Denda</td>
                                <td>: <strong class="text-danger">Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</strong>
                            </tr>
                            <tr>
                                <td>Sudah Dibayar</td>
                                <td>: <strong class="text-success">Rp {{ number_format($transaksi->denda_dibayar, 0, ',', '.') }}</strong>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Sisa Tagihan</strong></td>
                                <td>: <strong class="text-warning">Rp {{ number_format($sisa, 0, ',', '.') }}</strong>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Form Pembayaran -->
                    <form method="POST" action="{{ route('petugas.transaksi.bayar.proses', $transaksi->id) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="jumlah_bayar" 
                                   class="form-control form-control-lg" 
                                   value="{{ $sisa }}"
                                   min="1000"
                                   max="{{ $sisa }}"
                                   required>
                            <small class="text-muted">Minimal Rp 1.000, Maksimal Rp {{ number_format($sisa, 0, ',', '.') }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="metode_pembayaran" class="form-select form-select-lg" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="tunai">💰 Tunai</option>
                                <option value="transfer">🏦 Transfer Bank</option>
                                <option value="qris">📱 QRIS</option>
                            </select>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-secondary flex-grow-1">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-check me-1"></i> Bayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection