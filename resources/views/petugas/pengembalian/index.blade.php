@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Pengembalian Alat</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman_aktif as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->laptop->merk ?? '-' }} {{ $item->laptop->model ?? '' }}</td>
                        <td>{{ $item->tanggal_pinjam }}</td>
                        <td>{{ $item->tanggal_kembali_rencana }}</td>
                        <td>
                            <a href="{{ route('petugas.pengembalian.proses', $item->id) }}" class="btn btn-primary btn-sm">
                                Proses
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada peminjaman aktif</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection