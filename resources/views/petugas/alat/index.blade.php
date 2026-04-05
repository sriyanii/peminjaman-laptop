@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- ====================== STATISTIK ====================== -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stats-number">{{ $statistics['total_tools'] ?? 0 }}</div>
                <div class="stats-label">Total Alat</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ $statistics['available_tools'] ?? 0 }}</div>
                <div class="stats-label">Tersedia</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="stats-number">{{ $statistics['borrowed_tools'] ?? 0 }}</div>
                <div class="stats-label">Dipinjam</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number">{{ $statistics['maintenance_tools'] ?? 0 }}</div>
                <div class="stats-label">Maintenance</div>
            </div>
        </div>
    </div>

    <!-- Main content -->
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
                        Total: {{ $laptops->total() }} alat tersedia
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter Status -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('user.alat') }}" 
                           class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('user.alat', ['status' => 'tersedia']) }}" 
                           class="btn btn-outline-success {{ request('status') == 'tersedia' ? 'active' : '' }}">
                            Tersedia
                        </a>
                        <a href="{{ route('user.alat', ['status' => 'dipinjam']) }}" 
                           class="btn btn-outline-warning {{ request('status') == 'dipinjam' ? 'active' : '' }}">
                            Dipinjam
                        </a>
                        <a href="{{ route('user.alat', ['status' => 'rusak']) }}" 
                           class="btn btn-outline-danger {{ request('status') == 'rusak' ? 'active' : '' }}">
                            Rusak
                        </a>
                        <a href="{{ route('user.alat', ['status' => 'maintenance']) }}" 
                           class="btn btn-outline-info {{ request('status') == 'maintenance' ? 'active' : '' }}">
                            Maintenance
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.alat') }}" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari berdasarkan nama, merk, type, atau serial number..."
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
                                <span class="badge bg-secondary">{{ $laptop->kode_alat ?? '#' . $laptop->id }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($laptop->gambar)
                                    <img src="{{ asset($laptop->gambar) }}" 
                                         alt="{{ $laptop->nama_alat ?? $laptop->merk . ' ' . $laptop->model }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 150px; object-fit: cover; cursor: pointer;"
                                         onclick="showPhotoModal('{{ asset($laptop->gambar) }}', '{{ $laptop->nama_alat ?? $laptop->merk . ' ' . $laptop->model }}')">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 150px; cursor: pointer;"
                                         onclick="showPhotoModal('{{ asset('assets/img/no-image.png') }}', '{{ $laptop->nama_alat ?? $laptop->merk . ' ' . $laptop->model }}')">
                                        <i class="fas fa-laptop fa-3x text-secondary"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h5 class="card-title">{{ $laptop->nama_alat ?? $laptop->merk . ' ' . $laptop->model }}</h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-tag me-2"></i>{{ $laptop->merk }} {{ $laptop->type ?? $laptop->model }}<br>
                                @if($laptop->spesifikasi)
                                <i class="fas fa-info-circle me-2"></i>{{ Str::limit($laptop->spesifikasi, 80) }}<br>
                                @endif
                                @if($laptop->processor && $laptop->ram)
                                <i class="fas fa-microchip me-2"></i>{{ $laptop->processor }} | {{ $laptop->ram }} RAM<br>
                                @endif
                                @if($laptop->storage)
                                <i class="fas fa-database me-2"></i>{{ $laptop->storage }}<br>
                                @endif
                                <i class="fas fa-barcode me-2"></i>SN: {{ $laptop->serial_number }}
                            </p>
                            
                            <div class="mb-3">
                                @if($laptop->tanggal_pembelian ?? $laptop->tahun_pembelian)
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Pembelian: {{ $laptop->tanggal_pembelian ? date('Y', strtotime($laptop->tanggal_pembelian)) : $laptop->tahun_pembelian }}
                                </small><br>
                                @endif
                                
                                @if($laptop->lokasi)
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Lokasi: {{ $laptop->lokasi }}
                                </small>
                                @endif
                            </div>
                            
                            @if($laptop->kondisi)
                            <div class="mb-3">
                                @php
                                    $conditionBadge = [
                                        'baik' => 'success',
                                        'sedang' => 'warning',
                                        'rusak_ringan' => 'warning',
                                        'rusak_berat' => 'danger'
                                    ];
                                    $badgeColor = $conditionBadge[$laptop->kondisi] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">
                                    <i class="fas fa-{{ $laptop->kondisi == 'baik' ? 'check-circle' : ($laptop->kondisi == 'rusak_berat' ? 'times-circle' : 'exclamation-circle') }} me-1"></i>
                                    Kondisi: {{ ucfirst(str_replace('_', ' ', $laptop->kondisi)) }}
                                </span>
                            </div>
                            @endif
                            
                            @if($laptop->masa_garansi ?? $laptop->garansi)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Garansi: {{ $laptop->masa_garansi ?? $laptop->garansi }}
                                </small>
                            </div>
                            @endif
                            
                            @if($laptop->harga ?? $laptop->harga_sewa_harian)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Harga Sewa: Rp {{ number_format($laptop->harga ?? $laptop->harga_sewa_harian, 0, ',', '.') }}
                                </small>
                            </div>
                            @endif

                            <!-- Tombol Detail -->
                            <button type="button" 
                                    class="btn btn-outline-info btn-sm w-100 mb-2"
                                    onclick="loadDetail({{ $laptop->id }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal">
                                <i class="fas fa-eye me-2"></i>Lihat Detail
                            </button>

                            <!-- Cek peminjaman aktif user -->
                            @php
                                $peminjamanAktifCount = auth()->user()->peminjaman()
                                    ->whereIn('status', ['approved', 'aktif'])
                                    ->whereNull('tanggal_kembali')
                                    ->count();
                                
                                $bisaPinjam = $laptop->status == 'tersedia' && $peminjamanAktifCount < 2;
                            @endphp
                        </div>
                        <div class="card-footer bg-white">
                            @if($laptop->status == 'tersedia')
                                @if($peminjamanAktifCount >= 2)
                                    <button type="button" class="btn btn-warning w-100" 
                                            data-bs-toggle="tooltip" 
                                            title="Anda sudah memiliki 2 peminjaman aktif. Silakan kembalikan terlebih dahulu.">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Maksimal 2 Peminjaman
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#pinjamModal{{ $laptop->id }}">
                                        <i class="fas fa-hand-holding me-2"></i>Ajukan Peminjaman
                                    </button>
                                @endif
                            @elseif($laptop->status == 'dipinjam')
                                <button class="btn btn-secondary w-100 disabled" 
                                        data-bs-toggle="tooltip" 
                                        title="Alat sedang dipinjam oleh orang lain">
                                    <i class="fas fa-user-clock me-2"></i>Sedang Dipinjam
                                </button>
                            @elseif($laptop->status == 'maintenance')
                                <button class="btn btn-info w-100 disabled" 
                                        data-bs-toggle="tooltip" 
                                        title="Alat sedang dalam perawatan">
                                    <i class="fas fa-tools me-2"></i>Maintenance
                                </button>
                            @elseif($laptop->status == 'rusak')
                                <button class="btn btn-danger w-100 disabled" 
                                        data-bs-toggle="tooltip" 
                                        title="Alat sedang rusak dan tidak dapat dipinjam">
                                    <i class="fas fa-times-circle me-2"></i>Rusak
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 disabled">
                                    <i class="fas fa-ban me-2"></i>Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal for Peminjaman -->
                <div class="modal fade" id="pinjamModal{{ $laptop->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ajukan Peminjaman</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('user.peminjaman.store') }}" method="POST" id="pinjamForm{{ $laptop->id }}">
                                @csrf
                                <input type="hidden" name="laptop_id" value="{{ $laptop->id }}">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Alat/Laptop</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $laptop->nama_alat ?? $laptop->merk . ' ' . $laptop->model }} ({{ $laptop->kode_alat ?? '#' . $laptop->id }})" 
                                               readonly>
                                        <input type="hidden" name="nama_alat" value="{{ $laptop->nama_alat ?? $laptop->merk . ' ' . $laptop->model }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tanggal_pinjam{{ $laptop->id }}" class="form-label">
                                            <i class="fas fa-calendar-alt me-1"></i>Tanggal Pinjam *
                                        </label>
                                        <input type="date" class="form-control" 
                                               id="tanggal_pinjam{{ $laptop->id }}" 
                                               name="tanggal_pinjam"
                                               value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                                               min="{{ date('Y-m-d') }}"
                                               required>
                                        <small class="text-muted">Minimal hari ini</small>
                                        @error('tanggal_pinjam')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tanggal_kembali{{ $laptop->id }}" class="form-label">
                                            <i class="fas fa-calendar-check me-1"></i>Tanggal Kembali Rencana *
                                        </label>
                                        <input type="date" class="form-control" 
                                               id="tanggal_kembali{{ $laptop->id }}" 
                                               name="tanggal_kembali_rencana"
                                               value="{{ old('tanggal_kembali_rencana', date('Y-m-d', strtotime('+7 days'))) }}"
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                               required>
                                        <small class="text-muted" id="durationInfo{{ $laptop->id }}">Maksimal peminjaman 30 hari</small>
                                        @error('tanggal_kembali_rencana')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tujuan{{ $laptop->id }}" class="form-label">
                                            <i class="fas fa-bullseye me-1"></i>Tujuan Peminjaman *
                                        </label>
                                        <select class="form-select" id="tujuan{{ $laptop->id }}" name="tujuan" required>
                                            <option value="">Pilih Tujuan</option>
                                            <option value="meeting" {{ old('tujuan') == 'meeting' ? 'selected' : '' }}>Meeting/Rapat</option>
                                            <option value="presentasi" {{ old('tujuan') == 'presentasi' ? 'selected' : '' }}>Presentasi</option>
                                            <option value="training" {{ old('tujuan') == 'training' ? 'selected' : '' }}>Training/Pelatihan</option>
                                            <option value="work_from_home" {{ old('tujuan') == 'work_from_home' ? 'selected' : '' }}>Work From Home</option>
                                            <option value="proyek" {{ old('tujuan') == 'proyek' ? 'selected' : '' }}>Proyek Khusus</option>
                                            <option value="lainnya" {{ old('tujuan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        @error('tujuan')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="keterangan{{ $laptop->id }}" class="form-label">
                                            <i class="fas fa-sticky-note me-1"></i>Keterangan
                                        </label>
                                        <textarea class="form-control" 
                                                  id="keterangan{{ $laptop->id }}" 
                                                  name="keterangan" 
                                                  rows="3"
                                                  placeholder="Jelaskan tujuan penggunaan alat...">{{ old('keterangan') }}</textarea>
                                        <small class="text-muted">Opsional, tetapi membantu proses persetujuan</small>
                                        @error('keterangan')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Informasi Penting:</strong>
                                        <ul class="mt-2 mb-0">
                                            <li>Peminjaman akan ditinjau oleh petugas dalam 1x24 jam</li>
                                            <li>Maksimal 2 peminjaman aktif dalam waktu bersamaan</li>
                                            <li>Denda keterlambatan: <strong>Rp 10.000/hari</strong></li>
                                            <li>Pastikan alat dalam kondisi baik saat dikembalikan</li>
                                            <li>Hubungi petugas jika ada perubahan jadwal</li>
                                        </ul>
                                    </div>

                                    <!-- User info (hidden) -->
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    
                                    <!-- Default values for database -->
                                    <input type="hidden" name="status" value="pending">
                                    <input type="hidden" name="denda" value="0.00">
                                    <input type="hidden" name="is_denda_dibayar" value="0">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn{{ $laptop->id }}" onclick="playSubmitSound(this)">
                                        <i class="fas fa-paper-plane me-2"></i>Ajukan Peminjaman
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
                        Tidak ada alat/laptop yang tersedia untuk dipinjam.
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

<!-- ================ MODAL PREVIEW FOTO ================ -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalTitle">
                    <i class="fas fa-image me-2"></i>Foto Alat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="modalPhoto" src="" alt="Foto Alat" class="img-fluid" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- ================ MODAL DETAIL ALAT (AJAX) ================ -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-tools text-primary me-2"></i>Detail Alat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-contract me-2"></i>Syarat dan Ketentuan Peminjaman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Persyaratan Umum</h6>
                <ul>
                    <li>Peminjam harus memiliki akun terdaftar dan aktif</li>
                    <li>Maksimal 2 peminjaman aktif dalam waktu bersamaan</li>
                    <li>Peminjaman harus diajukan minimal 1 hari sebelumnya</li>
                </ul>
                
                <h6>2. Durasi Peminjaman</h6>
                <ul>
                    <li>Maksimal durasi peminjaman: 30 hari</li>
                    <li>Perpanjangan dapat diajukan maksimal 2 hari sebelum jatuh tempo</li>
                </ul>
                
                <h6>3. Denda Keterlambatan</h6>
                <ul>
                    <li>Denda keterlambatan: Rp 10.000 per hari</li>
                    <li>Denda dihitung mulai hari ke-31 atau setelah tanggal kembali rencana</li>
                    <li>Denda harus dilunasi sebelum melakukan peminjaman berikutnya</li>
                </ul>
                
                <h6>4. Tanggung Jawab Peminjam</h6>
                <ul>
                    <li>Peminjam bertanggung jawab penuh atas alat yang dipinjam</li>
                    <li>Wajib melaporkan kerusakan segera kepada petugas</li>
                    <li>Dilarang meminjamkan alat kepada pihak lain</li>
                </ul>
                
                <h6>5. Persetujuan</h6>
                <p>Dengan mencentang kotak persetujuan, Anda menyetujui semua syarat dan ketentuan di atas.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Sound effect function
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
    
    // Show photo modal
    function showPhotoModal(imageUrl, title) {
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        document.getElementById('modalPhoto').src = imageUrl;
        document.getElementById('photoModalTitle').innerHTML = '<i class="fas fa-image me-2"></i>Foto: ' + title;
        modal.show();
    }
    
    // Load detail via AJAX
    function loadDetail(id) {
        document.getElementById('detailModalBody').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;
        
        fetch(`/petugas/alat/${id}/detail`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data');
                }
                return response.json();
            })
            .then(laptop => {
                const formatRupiah = (value) => {
                    return value ? 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-';
                };
                
                const formatDate = (date) => {
                    if (!date) return '-';
                    const d = new Date(date);
                    return d.toLocaleDateString('id-ID');
                };
                
                let html = `
                    <div class="row">
                        <div class="col-md-12 text-center mb-3">
                            ${laptop.gambar ? 
                                `<img src="/${laptop.gambar}" alt="${laptop.merk} ${laptop.model}" style="max-height: 150px; object-fit: cover; cursor: pointer;" class="img-thumbnail" onclick="showPhotoModal('/${laptop.gambar}', '${laptop.merk} ${laptop.model}')">` : 
                                `<div class="bg-light p-3 d-inline-block rounded" style="cursor: pointer;" onclick="showPhotoModal('{{ asset('assets/img/no-image.png') }}', '${laptop.merk} ${laptop.model}')"><i class="fas fa-camera text-muted fa-3x"></i></div>`
                            }
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-row">
                                <div class="detail-label">ID</div>
                                <div class="detail-value">${laptop.id}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Merk</div>
                                <div class="detail-value">${laptop.merk}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Model</div>
                                <div class="detail-value">${laptop.model}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Processor</div>
                                <div class="detail-value">${laptop.processor || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">RAM</div>
                                <div class="detail-value">${laptop.ram || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Storage</div>
                                <div class="detail-value">${laptop.storage || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Serial Number</div>
                                <div class="detail-value">${laptop.serial_number || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Tahun Pembelian</div>
                                <div class="detail-value">${laptop.tahun_pembelian || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Lokasi</div>
                                <div class="detail-value">${laptop.lokasi || '-'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-row">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="badge bg-${laptop.status === 'tersedia' ? 'success' : (laptop.status === 'dipinjam' ? 'warning' : (laptop.status === 'rusak' ? 'danger' : 'info'))}">
                                        ${laptop.status}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Kondisi</div>
                                <div class="detail-value">
                                    <span class="badge bg-${laptop.kondisi === 'baik' ? 'success' : (laptop.kondisi === 'rusak_ringan' ? 'warning' : 'danger')}">
                                        ${laptop.kondisi ? laptop.kondisi.replace('_', ' ') : '-'}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Warna</div>
                                <div class="detail-value">${laptop.warna || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">OS</div>
                                <div class="detail-value">${laptop.os || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Baterai Kondisi</div>
                                <div class="detail-value">${laptop.baterai_kondisi || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Garansi</div>
                                <div class="detail-value">${laptop.garansi || '-'}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Garansi Berakhir</div>
                                <div class="detail-value">${formatDate(laptop.garansi_berakhir)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Harga Beli</div>
                                <div class="detail-value">${formatRupiah(laptop.harga_beli)}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Harga Sewa</div>
                                <div class="detail-value">${formatRupiah(laptop.harga_sewa_harian)}</div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="detail-row">
                                <div class="detail-label">Keterangan</div>
                                <div class="detail-value">${laptop.keterangan || '-'}</div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('detailModalBody').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('detailModalBody').innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                        <p class="mb-0">Gagal memuat data</p>
                        <small>${error.message}</small>
                    </div>
                `;
            });
    }

    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Set tanggal kembali min date based on tanggal pinjam
        $('input[name="tanggal_pinjam"]').change(function() {
            const laptopId = this.id.replace('tanggal_pinjam', '');
            const tanggalPinjam = $(this).val();
            
            if (!tanggalPinjam) return;
            
            const nextDay = new Date(tanggalPinjam);
            nextDay.setDate(nextDay.getDate() + 1);
            const nextDayStr = nextDay.toISOString().split('T')[0];
            
            const maxDate = new Date(tanggalPinjam);
            maxDate.setDate(maxDate.getDate() + 30);
            const maxDateStr = maxDate.toISOString().split('T')[0];
            
            $(`#tanggal_kembali${laptopId}`).attr('min', nextDayStr);
            $(`#tanggal_kembali${laptopId}`).attr('max', maxDateStr);
            
            const defaultDate = new Date(tanggalPinjam);
            defaultDate.setDate(defaultDate.getDate() + 7);
            const defaultDateStr = defaultDate.toISOString().split('T')[0];
            
            const currentValue = $(`#tanggal_kembali${laptopId}`).val();
            if (!currentValue || new Date(currentValue) < nextDay || new Date(currentValue) > maxDate) {
                $(`#tanggal_kembali${laptopId}`).val(defaultDateStr);
            }
            
            updateDurationInfo(laptopId);
        });
        
        $('input[name="tanggal_pinjam"], input[name="tanggal_kembali_rencana"]').change(function() {
            const laptopId = this.id.replace('tanggal_pinjam', '').replace('tanggal_kembali', '');
            updateDurationInfo(laptopId);
        });
        
        function updateDurationInfo(laptopId) {
            const tanggalPinjam = $(`#tanggal_pinjam${laptopId}`).val();
            const tanggalKembali = $(`#tanggal_kembali${laptopId}`).val();
            
            if (tanggalPinjam && tanggalKembali) {
                const pinjamDate = new Date(tanggalPinjam);
                const kembaliDate = new Date(tanggalKembali);
                const diffTime = Math.abs(kembaliDate - pinjamDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                const infoElement = $(`#durationInfo${laptopId}`);
                if (infoElement.length) {
                    if (diffDays > 30) {
                        infoElement.text(`Maksimal 30 hari (${diffDays} hari - MELEBIHI BATAS!)`);
                        infoElement.removeClass('text-muted').addClass('text-danger fw-bold');
                    } else {
                        infoElement.text(`Maksimal 30 hari (${diffDays} hari)`);
                        infoElement.removeClass('text-danger fw-bold').addClass('text-muted');
                    }
                }
            }
        }
        
        $('input[name="tanggal_pinjam"]').each(function() {
            $(this).trigger('change');
        });
        
        $('form[id^="pinjamForm"]').submit(function(e) {
            const form = $(this);
            const formId = form.attr('id').replace('pinjamForm', '');
            const tanggalPinjam = form.find('input[name="tanggal_pinjam"]').val();
            const tanggalKembali = form.find('input[name="tanggal_kembali_rencana"]').val();
            
            if (!tanggalPinjam || !tanggalKembali) {
                e.preventDefault();
                alert('Mohon isi tanggal pinjam dan tanggal kembali!');
                return false;
            }
            
            if (tanggalKembali <= tanggalPinjam) {
                e.preventDefault();
                alert('Tanggal kembali harus lebih besar dari tanggal pinjam!');
                return false;
            }
            
            const pinjamDate = new Date(tanggalPinjam);
            const kembaliDate = new Date(tanggalKembali);
            const diffTime = Math.abs(kembaliDate - pinjamDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 30) {
                e.preventDefault();
                alert('Maksimal peminjaman adalah 30 hari!');
                return false;
            }
            
            const tujuan = form.find('select[name="tujuan"]').val();
            if (!tujuan) {
                e.preventDefault();
                alert('Mohon pilih tujuan peminjaman!');
                return false;
            }
            
            var sound = document.getElementById('submitSound');
            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(e => console.log('Audio play failed:', e));
            }
            
            const submitBtn = $(`#submitBtn${formId}`);
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...');
            submitBtn.prop('disabled', true);
            
            return true;
        });
        
        $('.disabled').click(function(e) {
            e.preventDefault();
            return false;
        });
        
        setTimeout(function() {
            $('.alert-dismissible').fadeTo(500, 0).slideUp(500, function(){
                $(this).alert('close');
            });
        }, 5000);
        
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            const modalId = $(this).attr('id').replace('pinjamModal', '');
            if (modalId) {
                $(`#tanggal_pinjam${modalId}`).trigger('change');
            }
        });
    });
</script>

<style>
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        height: 100%;
        transition: transform 0.3s ease;
        border: none;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
    }
    
    .stats-number {
        font-size: 32px;
        font-weight: 700;
        margin: 10px 0;
        color: #2c3e50;
    }
    
    .stats-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .btn-group .btn.active {
        background-color: #3a86ff;
        color: white;
        border-color: #3a86ff;
    }
    
    .card {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 10px;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .modal-header .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #764ba2;
        box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
    }
    
    .btn-clicked {
        transform: scale(0.95);
        transition: transform 0.1s ease;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 8px;
    }
    
    .detail-label {
        width: 40%;
        font-weight: 600;
        color: #495057;
    }
    
    .detail-value {
        width: 60%;
        color: #212529;
    }
    
    @media (max-width: 768px) {
        .col-md-4 {
            width: 100%;
        }
        
        .modal-dialog {
            margin: 10px;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .stats-card {
            padding: 15px;
        }
        
        .stats-number {
            font-size: 24px;
        }
    }
</style>
@endsection