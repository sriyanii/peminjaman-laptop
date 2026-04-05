@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-md-12 px-md-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-history me-2"></i>Riwayat Peminjaman
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                @php
                    $total_selesai = $riwayat->where('status', 'selesai')->count();
                    $total_ditolak = $riwayat->where('status', 'ditolak')->count();
                    $total_denda = $riwayat->sum('denda');
                    $denda_lunas = $riwayat->where('is_denda_dibayar', 1)->sum('denda');
                @endphp
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h1 class="display-5 text-primary">{{ $total_selesai }}</h1>
                            <h6 class="card-subtitle mb-2 text-muted">Selesai</h6>
                            <small class="text-muted">Peminjaman selesai</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body text-center">
                            <h1 class="display-5 text-danger">{{ $total_ditolak }}</h1>
                            <h6 class="card-subtitle mb-2 text-muted">Ditolak</h6>
                            <small class="text-muted">Peminjaman ditolak</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <h1 class="display-6 text-warning">Rp {{ number_format($total_denda, 0, ',', '.') }}</h1>
                            <h6 class="card-subtitle mb-2 text-muted">Total Denda</h6>
                            <small class="text-muted">Keseluruhan denda</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h1 class="display-6 text-success">Rp {{ number_format($denda_lunas, 0, ',', '.') }}</h1>
                            <h6 class="card-subtitle mb-2 text-muted">Denda Lunas</h6>
                            <small class="text-muted">Denda telah dibayar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- Table Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Riwayat
                            </h5>
                            <span class="badge bg-primary">
                                {{ $riwayat->total() }} Data
                            </span>
                        </div>
                        <div class="card-body p-0">
                            @if($riwayat->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Kode</th>
                                            <th>Laptop</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th width="100">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($riwayat as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $item->kode_peminjaman }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong class="d-block">{{ $item->laptop->merk ?? '-' }}</strong>
                                                    <small class="text-muted">{{ $item->laptop->model ?? '' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted d-block">Pinjam:</small>
                                                    <strong>{{ date('d/m/Y', strtotime($item->tanggal_pinjam)) }}</strong>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted d-block">Kembali:</small>
                                                    <strong>
                                                        @if($item->tanggal_kembali)
                                                            {{ date('d/m/Y', strtotime($item->tanggal_kembali)) }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </strong>
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->status == 'selesai')
                                                    <span class="badge bg-success">Selesai</span>
                                                @elseif($item->status == 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                                @endif
                                                
                                                @if($item->denda > 0)
                                                    <div class="mt-1">
                                                        <span class="badge bg-danger">
                                                            Denda: Rp {{ number_format($item->denda, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal{{ $item->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                <h4>Belum ada riwayat peminjaman</h4>
                                <p class="text-muted">Riwayat akan muncul setelah peminjaman selesai atau ditolak</p>
                                <a href="{{ route('user.alat') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-laptop me-2"></i>Pinjam Laptop
                                </a>
                            </div>
                            @endif
                        </div>
                        
                        @if($riwayat->count() > 0)
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    Menampilkan {{ $riwayat->firstItem() }} - {{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data
                                </div>
                                <div>
                                    {{ $riwayat->links() }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Information Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary">Legenda Status:</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success me-2" style="width: 20px; height: 20px;"></span>
                                    <small>Selesai - Peminjaman telah selesai</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-danger me-2" style="width: 20px; height: 20px;"></span>
                                    <small>Ditolak - Tidak disetujui petugas</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2" style="width: 20px; height: 20px;"></span>
                                    <small>Belum Lunas - Denda belum dibayar</small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div>
                                <h6 class="text-primary">Perhatian:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        <small>Bayar denda sebelum pinjam baru</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-clock text-info me-2"></i>
                                        <small>Riwayat tersimpan 1 tahun</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-user-check text-success me-2"></i>
                                        <small>Hubungi petugas untuk denda</small>
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <small>Verifikasi setelah pengembalian</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Detail Modals -->
@foreach($riwayat as $item)
<div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Peminjaman</label>
                        <input type="text" class="form-control" value="{{ $item->kode_peminjaman }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        @if($item->status == 'selesai')
                            <input type="text" class="form-control bg-success text-white" value="Selesai" readonly>
                        @elseif($item->status == 'ditolak')
                            <input type="text" class="form-control bg-danger text-white" value="Ditolak" readonly>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Laptop</label>
                    <input type="text" class="form-control" 
                           value="{{ $item->laptop->merk ?? '-' }} {{ $item->laptop->model ?? '' }}" readonly>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="text" class="form-control" 
                               value="{{ date('d/m/Y', strtotime($item->tanggal_pinjam)) }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="text" class="form-control" 
                               value="{{ $item->tanggal_kembali ? date('d/m/Y', strtotime($item->tanggal_kembali)) : '-' }}" readonly>
                    </div>
                </div>
                
                @if($item->denda > 0)
                <div class="mb-3">
                    <label class="form-label">Denda</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" 
                               value="{{ number_format($item->denda, 0, ',', '.') }}" readonly>
                        <span class="input-group-text">
                            @if($item->is_denda_dibayar)
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning">Belum Lunas</span>
                            @endif
                        </span>
                    </div>
                </div>
                @endif
                
                @if($item->catatan_pengembalian)
                <div class="mb-3">
                    <label class="form-label">Catatan Pengembalian</label>
                    <textarea class="form-control" rows="3" readonly>{{ $item->catatan_pengembalian }}</textarea>
                </div>
                @endif
                
                @if($item->alasan_ditolak)
                <div class="mb-3">
                    <label class="form-label">Alasan Ditolak</label>
                    <textarea class="form-control" rows="3" readonly>{{ $item->alasan_ditolak }}</textarea>
                </div>
                @endif
                
                @if($item->keterangan)
                <div class="mb-3">
                    <label class="form-label">Keterangan Peminjaman</label>
                    <textarea class="form-control" rows="2" readonly>{{ $item->keterangan }}</textarea>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
@endsection