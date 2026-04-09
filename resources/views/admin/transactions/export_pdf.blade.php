<!DOCTYPE html>
<html>
<head>
    <title>Export PDF Transaksi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Transaksi Denda</h2>
    <p>Periode: {{ request('start_date') }} - {{ request('end_date') }}</p>
    <p>Status: {{ request('status_pembayaran') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Nama Peminjam</th>
                <th>Jumlah Denda</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $key => $transaksi)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $transaksi->id }}</td>
                <td>{{ $transaksi->user->name ?? 'N/A' }}</td>
                <td>Rp {{ number_format($transaksi->jumlah_denda ?? 0, 0, ',', '.') }}</td>
                <td>{{ $transaksi->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>