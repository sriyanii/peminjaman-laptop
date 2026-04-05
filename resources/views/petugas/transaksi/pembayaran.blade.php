@extends('layouts.app')

@section('title', 'Proses Pembayaran Denda')
@section('header-icon', 'fas fa-credit-card')
@section('header-title', 'Proses Pembayaran Denda')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Form Pembayaran Denda
                    </h5>
                </div>
                <div class="card-body">
                    
                    <!-- Informasi Transaksi -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Kode Transaksi:</strong> #{{ $transaksi->id }}<br>
                                <strong>Peminjam:</strong> {{ $transaksi->user->name }}<br>
                                <strong>Email:</strong> {{ $transaksi->user->email }}
                            </div>
                            <div class="col-md-6">
                                <strong>Total Denda:</strong> Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}<br>
                                <strong>Sudah Dibayar:</strong> Rp {{ number_format($transaksi->denda_dibayar, 0, ',', '.') }}<br>
                                <strong>Sisa:</strong> Rp {{ number_format($transaksi->total_denda - $transaksi->denda_dibayar, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Form Pembayaran -->
                    <form method="POST" action="{{ route('petugas.transaksi.proses-pembayaran', $transaksi->id) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="jumlah_dibayar" class="form-label">Jumlah Dibayar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('jumlah_dibayar') is-invalid @enderror" 
                                       id="jumlah_dibayar" 
                                       name="jumlah_dibayar" 
                                       value="{{ old('jumlah_dibayar', $transaksi->total_denda - $transaksi->denda_dibayar) }}"
                                       min="{{ $transaksi->total_denda - $transaksi->denda_dibayar }}"
                                       step="1000"
                                       required>
                                @error('jumlah_dibayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Minimal pembayaran: Rp {{ number_format($transaksi->total_denda - $transaksi->denda_dibayar, 0, ',', '.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-control @error('metode_pembayaran') is-invalid @enderror" 
                                    id="metode_pembayaran" 
                                    name="metode_pembayaran" 
                                    required>
                                <option value="">Pilih Metode</option>
                                <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="qris" {{ old('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                            </select>
                            @error('metode_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="2">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection