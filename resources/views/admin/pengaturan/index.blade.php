@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog mr-2"></i>Pengaturan Aplikasi
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.pengaturan.simpan') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                    <i class="fas fa-info-circle mr-1"></i>Umum
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="appearance-tab" data-toggle="tab" href="#appearance" role="tab">
                                    <i class="fas fa-palette mr-1"></i>Tampilan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">
                                    <i class="fas fa-server mr-1"></i>Sistem
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="backup-tab" data-toggle="tab" href="#backup" role="tab">
                                    <i class="fas fa-database mr-1"></i>Backup
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="settingsTabContent">
                            <!-- Tab Umum -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_aplikasi">Nama Aplikasi</label>
                                            <input type="text" class="form-control" id="nama_aplikasi" 
                                                   name="nama_aplikasi" 
                                                   value="{{ old('nama_aplikasi', $settings['nama_aplikasi'] ?? '') }}"
                                                   placeholder="Masukkan nama aplikasi">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email_admin">Email Admin</label>
                                            <input type="email" class="form-control" id="email_admin" 
                                                   name="email_admin" 
                                                   value="{{ old('email_admin', $settings['email_admin'] ?? '') }}"
                                                   placeholder="admin@example.com">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telepon_admin">Telepon Admin</label>
                                            <input type="text" class="form-control" id="telepon_admin" 
                                                   name="telepon_admin" 
                                                   value="{{ old('telepon_admin', $settings['telepon_admin'] ?? '') }}"
                                                   placeholder="081234567890">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="batas_peminjaman">Batas Peminjaman (hari)</label>
                                            <input type="number" class="form-control" id="batas_peminjaman" 
                                                   name="batas_peminjaman" min="1"
                                                   value="{{ old('batas_peminjaman', $settings['batas_peminjaman'] ?? 7) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="denda_per_hari">Denda Per Hari (Rp)</label>
                                            <input type="number" class="form-control" id="denda_per_hari" 
                                                   name="denda_per_hari" min="0"
                                                   value="{{ old('denda_per_hari', $settings['denda_per_hari'] ?? 5000) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="logo_aplikasi">Logo Aplikasi</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="logo_aplikasi" 
                                                       name="logo_aplikasi" accept="image/*">
                                                <label class="custom-file-label" for="logo_aplikasi">Pilih file logo...</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Ukuran maksimal 2MB. Format: jpeg, png, jpg, gif, svg
                                            </small>
                                            @if(isset($settings['logo_aplikasi']) && $settings['logo_aplikasi'])
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $settings['logo_aplikasi']) }}" 
                                                         alt="Logo Aplikasi" class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="favicon">Favicon</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="favicon" 
                                                       name="favicon" accept="image/x-icon,image/png">
                                                <label class="custom-file-label" for="favicon">Pilih file favicon...</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Ukuran maksimal 1MB. Format: ico, png
                                            </small>
                                            @if(isset($settings['favicon']) && $settings['favicon'])
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $settings['favicon']) }}" 
                                                         alt="Favicon" class="img-thumbnail" style="max-height: 50px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Tampilan -->
                            <div class="tab-pane fade" id="appearance" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="warna_utama">Warna Utama</label>
                                            <input type="color" class="form-control" id="warna_utama" 
                                                   name="warna_utama" 
                                                   value="{{ old('warna_utama', $settings['warna_utama'] ?? '#3b82f6') }}"
                                                   style="height: 40px; padding: 5px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timezone">Zona Waktu</label>
                                            <select class="form-control" id="timezone" name="timezone">
                                                <option value="Asia/Jakarta" {{ ($settings['timezone'] ?? '') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Makassar" {{ ($settings['timezone'] ?? '') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                                <option value="Asia/Jayapura" {{ ($settings['timezone'] ?? '') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                                <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Sistem -->
                            <div class="tab-pane fade" id="system" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input type="checkbox" class="form-check-input" id="maintenance_mode" 
                                                   name="maintenance_mode" value="1"
                                                   {{ ($settings['maintenance_mode'] ?? 'false') == 'true' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="maintenance_mode">
                                                Mode Maintenance
                                            </label>
                                            <small class="form-text text-muted">
                                                Aktifkan untuk menutup akses publik ke aplikasi
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Pemeliharaan Sistem</h6>
                                        
                                        <button type="button" class="btn btn-warning btn-sm" onclick="clearCache()">
                                            <i class="fas fa-broom mr-1"></i>Clear Cache
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm ml-2" 
                                                onclick="if(confirm('Yakin ingin mereset semua pengaturan ke default?')) resetSettings()">
                                            <i class="fas fa-redo mr-1"></i>Reset Pengaturan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Backup -->
                            <div class="tab-pane fade" id="backup" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Backup otomatis dibuat setiap hari pada pukul 00:00
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary" onclick="backupDatabase()">
                                            <i class="fas fa-download mr-1"></i>Backup Database Sekarang
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h6>Daftar Backup Terbaru</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Nama File</th>
                                                        <th>Tanggal</th>
                                                        <th>Ukuran</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            Tidak ada backup tersedia
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Simpan Pengaturan
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Untuk menampilkan nama file di input file
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });

    // Fungsi untuk clear cache
    function clearCache() {
        if (confirm('Yakin ingin membersihkan cache?')) {
            window.location.href = "{{ route('admin.pengaturan.clear-cache') }}";
        }
    }

    // Fungsi untuk reset settings
    function resetSettings() {
        window.location.href = "{{ route('admin.pengaturan.reset') }}";
    }

    // Fungsi untuk backup database
    function backupDatabase() {
        fetch("{{ route('admin.pengaturan.backup') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Backup berhasil dibuat: ' + data.filename);
        })
        .catch(error => {
            alert('Terjadi kesalahan saat membuat backup');
            console.error(error);
        });
    }
</script>
@endpush