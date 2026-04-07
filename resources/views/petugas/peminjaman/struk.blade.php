{{-- resources/views/petugas/peminjaman/struk.blade.php --}}
@extends('layouts.app')

@section('title', 'Struk Pengembalian')
@section('header-icon', 'fas fa-receipt')
@section('header-title', 'Struk Pengembalian')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/petugas.css') }}">
@endpush

@section('content')
<div class="struk-container">
    <div class="struk">
        <div class="text-center">
            <h4>PEMINJAMAN ALAT</h4>
            <p>Jl. Contoh No. 123, Kota<br>Telp: (021) 1234567</p>
            <div class="border-bottom"></div>
            <p><strong>STRUK PENGEMBALIAN</strong></p>
            <div class="border-bottom"></div>
        </div>
        
        <table class="table">
            <tr>
                <td width="40%">Kode Transaksi</td>
                <td width="60%">: {{ $denda->id ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ now()->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td>: {{ auth()->user()->name ?? '-' }}</td>
            </tr>
        </table>
        
        <div class="border-bottom"></div>
        
        <table class="table">
            <tr>
                <td>Kode Peminjaman</td>
                <td>: {{ $peminjaman->kode_peminjaman ?? '-' }}</td>
            </tr>
            <tr>
                <td>Peminjam</td>
                <td>: {{ $peminjaman->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alat</td>
                <td>: {{ $peminjaman->laptop->merk ?? $peminjaman->nama_alat ?? '-' }} {{ $peminjaman->laptop->model ?? '' }}</td>
            </tr>
        </table>
        
        <div class="border-bottom"></div>
        
        <table class="table">
            <tr>
                <td>Tanggal Pinjam</td>
                <td class="text-right">: {{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Rencana Kembali</td>
                <td class="text-right">: {{ $peminjaman->tanggal_kembali_rencana ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Kembali</td>
                <td class="text-right">: {{ $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') : now()->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Lama Sewa</td>
                <td class="text-right">: {{ $peminjaman->lama_hari ?? 0 }} hari</td>
            </tr>
        </table>
        
        <div class="border-bottom"></div>
        
        <table class="table">
            <tr>
                <td>Harga Sewa</td>
                <td class="text-right">: Rp {{ number_format($peminjaman->harga_sewa ?? 0, 0, ',', '.') }}</td>
            </tr>
            @if(isset($denda_telat) && $denda_telat > 0)
            <tr>
                <td>Denda Telat ({{ $telat_hari ?? 0 }} hari)</td>
                <td class="text-right">: Rp {{ number_format($denda_telat, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if(isset($denda_kerusakan) && $denda_kerusakan > 0)
            <tr>
                <td>Denda Kerusakan</td>
                <td class="text-right">: Rp {{ number_format($denda_kerusakan, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total">
                <td><strong>TOTAL TAGIHAN</strong></td>
                <td class="text-right"><strong>: Rp {{ number_format($total_tagihan ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Jumlah Bayar</td>
                <td class="text-right">: Rp {{ number_format($jumlah_dibayar ?? 0, 0, ',', '.') }}</td>
            </tr>
            @if(isset($jumlah_dibayar) && isset($total_tagihan) && ($jumlah_dibayar - $total_tagihan) > 0)
            <tr>
                <td>Kembalian</td>
                <td class="text-right">: Rp {{ number_format($jumlah_dibayar - $total_tagihan, 0, ',', '.') }}</td>
            </tr>
            @elseif(isset($jumlah_dibayar) && isset($total_tagihan) && ($jumlah_dibayar - $total_tagihan) < 0)
            <tr>
                <td>Sisa Tagihan</td>
                <td class="text-right">: Rp {{ number_format($total_tagihan - $jumlah_dibayar, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
        
        <div class="border-bottom"></div>
        
        <div class="text-center">
            <p>Terima kasih<br>Barang sudah dikembalikan</p>
            <div class="border-bottom"></div>
            <div class="no-print">
                <button onclick="window.print()" class="btn-petugas btn-petugas-primary">
                    <i class="fas fa-print me-2"></i> Cetak Struk
                </button>
                <a href="{{ route('petugas.peminjaman.index') }}" class="btn-petugas btn-petugas-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto print when page loads (optional)
    // window.onload = function() {
    //     setTimeout(function() {
    //         window.print();
    //     }, 500);
    // };
    
    // Print function
    function printStruk() {
        window.print();
    }
</script>
@endpush