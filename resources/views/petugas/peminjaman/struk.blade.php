{{-- resources/views/petugas/peminjaman/struk.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Struk Pengembalian</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
        }
        .struk {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .border-bottom {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        .table {
            width: 100%;
            margin: 10px 0;
        }
        .table td {
            padding: 4px 0;
        }
        .total {
            font-size: 14px;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="text-center">
            <h4>PEMINJAMAN LAPTOP</h4>
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
                <td>: {{ $peminjaman->kode_peminjaman }}</td>
            </tr>
            <tr>
                <td>Peminjam</td>
                <td>: {{ $peminjaman->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Laptop</td>
                <td>: {{ $peminjaman->laptop->merk ?? '-' }} {{ $peminjaman->laptop->model ?? '-' }}</td>
            </tr>
        </table>
        
        <div class="border-bottom"></div>
        
        <table class="table">
            <tr>
                <td>Tanggal Pinjam</td>
                <td class="text-right">: {{ Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Rencana Kembali</td>
                <td class="text-right">: {{ Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Kembali</td>
                <td class="text-right">: {{ Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Lama Sewa</td>
                <td class="text-right">: {{ $peminjaman->lama_hari }} hari</td>
            </tr>
        </table>
        
        <div class="border-bottom"></div>
        
        <table class="table">
            <tr>
                <td>Harga Sewa</td>
                <td class="text-right">: Rp {{ number_format($peminjaman->harga_sewa, 0, ',', '.') }}</td>
            </tr>
            @if($denda_telat > 0)
            <tr>
                <td>Denda Telat ({{ $telat_hari }} hari)</td>
                <td class="text-right">: Rp {{ number_format($denda_telat, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($denda_kerusakan > 0)
            <tr>
                <td>Denda Kerusakan</td>
                <td class="text-right">: Rp {{ number_format($denda_kerusakan, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total">
                <td><strong>TOTAL TAGIHAN</strong></td>
                <td class="text-right"><strong>: Rp {{ number_format($total_tagihan, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Jumlah Bayar</td>
                <td class="text-right">: Rp {{ number_format($jumlah_dibayar, 0, ',', '.') }}</td>
            </tr>
            @if($jumlah_dibayar - $total_tagihan > 0)
            <tr>
                <td>Kembalian</td>
                <td class="text-right">: Rp {{ number_format($jumlah_dibayar - $total_tagihan, 0, ',', '.') }}</td>
            </tr>
            @elseif($jumlah_dibayar - $total_tagihan < 0)
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
            <p class="no-print">
                <button onclick="window.print()" style="margin-top: 10px;">Cetak Struk</button>
            </p>
        </div>
    </div>
</body>
</html>