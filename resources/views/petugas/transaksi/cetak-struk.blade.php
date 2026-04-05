<div class="struk-container p-4" style="max-width: 400px; margin: 0 auto; font-family: 'Courier New', monospace;">
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-0">STRUK PEMBAYARAN DENDA</h4>
        <p class="mb-0">Inventaris Lab</p>
        <small>{{ now()->format('d/m/Y H:i:s') }}</small>
    </div>

    <hr style="border-top: 2px dashed #000;">

    <div class="mb-3">
        <table width="100%" style="font-size: 14px;">
            <tr>
                <td>No. Transaksi</td>
                <td class="text-end fw-bold">#TRX-{{ $transaksi->id }}</td>
            </tr>
            <tr>
                <td>No. Peminjaman</td>
                <td class="text-end">#{{ $transaksi->peminjaman->kode_peminjaman ?? 'PJ-'.$transaksi->peminjaman_id }}</td>
            </tr>
            <tr>
                <td>Peminjam</td>
                <td class="text-end">{{ $transaksi->user->name }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td class="text-end">{{ $transaksi->petugas->name ?? auth()->user()->name }}</td>
            </tr>
        </table>
    </div>

    <hr style="border-top: 1px solid #000;">

    <div class="mb-3">
        <h6 class="fw-bold">DETAIL DENDA</h6>
        <table width="100%" style="font-size: 14px;">
            <tr>
                <td>Denda Terlambat</td>
                <td class="text-end">Rp {{ number_format($transaksi->peminjaman->denda_terlambat ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Denda Kerusakan</td>
                <td class="text-end">Rp {{ number_format($transaksi->peminjaman->denda_kerusakan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Denda Hilang</td>
                <td class="text-end">Rp {{ number_format($transaksi->peminjaman->denda_hilang ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2"><hr style="border-top: 1px solid #000;"></td>
            </tr>
            <tr>
                <td><strong>TOTAL DENDA</strong></td>
                <td class="text-end"><strong>Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">PEMBAYARAN</h6>
        <table width="100%" style="font-size: 14px;">
            <tr>
                <td>Metode</td>
                <td class="text-end">{{ strtoupper($transaksi->metode_pembayaran ?? 'TUNAI') }}</td>
            </tr>
            <tr>
                <td>Dibayar</td>
                <td class="text-end">Rp {{ number_format($transaksi->denda_dibayar, 0, ',', '.') }}</td>
            </tr>
            @php
                $kembalian = $transaksi->denda_dibayar - $transaksi->total_denda;
            @endphp
            @if($kembalian > 0)
            <tr>
                <td>Kembalian</td>
                <td class="text-end">Rp {{ number_format($kembalian, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold">KONDISI BARANG</h6>
        <p class="mb-1">{{ ucfirst(str_replace('_', ' ', $transaksi->kondisi_barang)) }}</p>
        @if($transaksi->catatan_cek)
            <small class="text-muted">{{ $transaksi->catatan_cek }}</small>
        @endif
    </div>

    <hr style="border-top: 2px dashed #000;">

    <div class="text-center">
        <p class="mb-0">Terima kasih telah mematuhi peraturan</p>
        <small>Barang yang rusak/hilang menjadi tanggung jawab peminjam</small>
    </div>

    <div class="text-center mt-3" style="font-size: 12px;">
        <p class="mb-0">Struk ini merupakan bukti pembayaran yang sah</p>
        <p class="mb-0">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</div>

<style>
@media print {
    .struk-container {
        padding: 0 !important;
    }
    hr {
        border-top: 2px dashed #000 !important;
    }
}
</style>