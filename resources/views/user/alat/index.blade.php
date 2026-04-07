@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-peminjaman.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4 g-3">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-laptop me-2"></i>Daftar Alat
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('user.peminjaman.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Peminjaman Saya
                        </a>
                    </div>
                    <span class="text-muted">
                        Total: {{ $laptops->total() }} alat
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.alat') }}" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari berdasarkan merk, model, processor, atau serial number..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audio element untuk sound effect -->
            <audio id="submitSound" preload="auto">
                <source src="{{ asset('audio/audio1.wav') }}" type="audio/wav">
                Browser Anda tidak mendukung audio.
            </audio>

            <!-- Alat Cards -->
            <div class="row">
                @forelse($laptops as $laptop)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    @if($laptop->status == 'tersedia')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Tersedia
                                        </span>
                                    @elseif($laptop->status == 'dipinjam')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-user-clock me-1"></i>Dipinjam
                                        </span>
                                    @elseif($laptop->status == 'maintenance')
                                        <span class="badge bg-info">
                                            <i class="fas fa-tools me-1"></i>Maintenance
                                        </span>
                                    @elseif($laptop->status == 'rusak')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Rusak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $laptop->status }}</span>
                                    @endif
                                </h6>
                                <span class="badge bg-secondary">{{ $laptop->serial_number }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- GAMBAR -->
                            <div class="text-center mb-3">
                                @if($laptop->gambar && file_exists(public_path($laptop->gambar)))
                                    <img src="{{ asset($laptop->gambar) }}" 
                                         alt="{{ $laptop->merk }} {{ $laptop->model }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 150px; object-fit: cover; cursor: pointer;"
                                         onclick="showPhotoModal('{{ asset($laptop->gambar) }}', '{{ $laptop->merk }} {{ $laptop->model }}')">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 150px; cursor: pointer;"
                                         onclick="showPhotoModal('{{ asset('images/no-image.png') }}', '{{ $laptop->merk }} {{ $laptop->model }}')">
                                        <i class="fas fa-laptop fa-3x text-secondary"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- MERK & MODEL -->
                            <h5 class="card-title">{{ $laptop->merk }} {{ $laptop->model }}</h5>
                            
                            <!-- SPESIFIKASI -->
                            <p class="card-text text-muted">
                                <i class="fas fa-microchip me-2"></i>{{ $laptop->processor }}<br>
                                <i class="fas fa-memory me-2"></i>{{ $laptop->ram }} RAM | {{ $laptop->storage }}<br>
                                <i class="fas fa-barcode me-2"></i>SN: {{ $laptop->serial_number }}
                            </p>
                            
                            <!-- LOKASI -->
                            @if($laptop->lokasi)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Lokasi: {{ $laptop->lokasi }}
                                </small>
                            </div>
                            @endif
                            
                            <!-- KONDISI -->
                            @if($laptop->kondisi)
                            <div class="mb-2">
                                @php
                                    $conditionBadge = [
                                        'baik' => 'success',
                                        'rusak_ringan' => 'warning',
                                        'rusak_berat' => 'danger'
                                    ];
                                    $badgeColor = $conditionBadge[$laptop->kondisi] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">
                                    Kondisi: {{ str_replace('_', ' ', ucfirst($laptop->kondisi)) }}
                                </span>
                            </div>
                            @endif
                            
                            <!-- TAHUN PEMBELIAN -->
                            @if($laptop->tahun_pembelian)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Thn. Pembelian: {{ $laptop->tahun_pembelian }}
                                </small>
                            </div>
                            @endif
                            
                            <!-- GARANSI -->
                            @if($laptop->garansi)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Garansi: {{ $laptop->garansi }}
                                </small>
                            </div>
                            @endif
                            
                            <!-- HARGA SEWA -->
                            @if($laptop->harga_sewa_harian)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Sewa: Rp {{ number_format($laptop->harga_sewa_harian, 0, ',', '.') }}/hari
                                </small>
                            </div>
                            @endif

                            <!-- Cek peminjaman aktif user -->
                            @php
                                $peminjamanAktifCount = auth()->user()->peminjaman()
                                    ->whereIn('status', ['approved', 'aktif'])
                                    ->whereNull('tanggal_kembali')
                                    ->count();
                            @endphp
                        </div>
                        <div class="card-footer bg-white">
                            @if($laptop->status == 'tersedia')
                                @if($peminjamanAktifCount >= 2)
                                    <button type="button" class="btn btn-warning w-100" disabled>
                                        <i class="fas fa-exclamation-triangle me-2"></i>Maksimal 2 Peminjaman
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#pinjamModal{{ $laptop->id }}">
                                        <i class="fas fa-hand-holding me-2"></i>Ajukan Peminjaman
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-secondary w-100 disabled">
                                    <i class="fas fa-ban me-2"></i>Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Peminjaman -->
                <div class="modal fade" id="pinjamModal{{ $laptop->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ajukan Peminjaman</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('user.peminjaman.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="laptop_id" value="{{ $laptop->id }}">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Alat/Laptop</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $laptop->merk }} {{ $laptop->model }}" readonly>
                                        <input type="hidden" name="nama_alat" value="{{ $laptop->merk }} {{ $laptop->model }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Pinjam *</label>
                                        <input type="date" name="tanggal_pinjam" class="form-control" 
                                               value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Kembali Rencana *</label>
                                        <input type="date" name="tanggal_kembali_rencana" class="form-control" 
                                               value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               max="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tujuan Peminjaman *</label>
                                        <select name="tujuan" class="form-select" required>
                                            <option value="">Pilih Tujuan</option>
                                            <option value="meeting">Meeting/Rapat</option>
                                            <option value="presentasi">Presentasi</option>
                                            <option value="training">Training/Pelatihan</option>
                                            <option value="work_from_home">Work From Home</option>
                                            <option value="proyek">Proyek Khusus</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Informasi Penting:</strong>
                                        <ul class="mt-2 mb-0">
                                            <li>Peminjaman akan ditinjau oleh petugas dalam 1x24 jam</li>
                                            <li>Maksimal 2 peminjaman aktif dalam waktu bersamaan</li>
                                            <li>Denda keterlambatan: <strong>Rp 10.000/hari</strong></li>
                                        </ul>
                                    </div>

                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    <input type="hidden" name="status" value="pending">
                                    <input type="hidden" name="denda" value="0">
                                    <input type="hidden" name="is_denda_dibayar" value="0">
                                </div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary" 
            onclick="var s=new Audio('{{ asset("audio/audio2.wav") }}'); s.volume=0.7; s.play(); setTimeout(()=>{s.currentTime=0; s.play();}, 500); setTimeout(()=>{s.currentTime=0; s.play();}, 1000); setTimeout(()=>{s.currentTime=0; s.play();}, 1500); setTimeout(()=>{s.currentTime=0; s.play();}, 2000); setTimeout(()=>{s.currentTime=0; s.play();}, 2500);">
        Ajukan
    </button>
</div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada alat/laptop yang tersedia.
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($laptops->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $laptops->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Preview Foto -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" alt="Foto Alat" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
    function showPhotoModal(imageUrl, title) {
        document.getElementById('modalPhoto').src = imageUrl;
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        modal.show();
    }
    
    function playSubmitSound(button) {
        var sound = document.getElementById('submitSound');
        if (sound) {
            sound.currentTime = 0;
            sound.play().catch(e => console.log('Audio play failed:', e));
        }
        button.classList.add('btn-clicked');
        setTimeout(() => {
            button.classList.remove('btn-clicked');
        }, 100);
    }
</script>
@endsection