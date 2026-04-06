@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Form Pengembalian</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Peminjam:</strong> {{ $peminjaman->user->name }}<br>
                <strong>Alat:</strong> {{ $peminjaman->laptop->merk }} {{ $peminjaman->laptop->model }}<br>
                <strong>Serial Number:</strong> {{ $peminjaman->laptop->serial_number }}<br>
                <strong>Tgl Pinjam:</strong> {{ $peminjaman->tanggal_pinjam }}<br>
                <strong>Rencana Kembali:</strong> {{ $peminjaman->tanggal_kembali_rencanda }}
            </div>
            
            <form method="POST" action="{{ route('petugas.pengembalian.proses.store', $peminjaman->id) }}">
                @csrf
                
                <div class="mb-3">
                    <label>Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                
                <div class="mb-3">
                    <label>Kondisi</label>
                    <select name="kondisi" class="form-control" required>
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                        <option value="hilang">Hilang</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label>Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Proses</button>
                <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection