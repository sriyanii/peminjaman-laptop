@php
    $mode = isset($borrowing) ? ($borrowing->id ? 'edit' : 'show') : 'create';
    $title = $mode == 'show' ? 'Detail Peminjaman #' . $borrowing->id : 
             ($mode == 'edit' ? 'Edit Peminjaman #' . $borrowing->id : 'Buat Peminjaman Baru');
    $icon = $mode == 'show' ? 'fas fa-eye' : 
            ($mode == 'edit' ? 'fas fa-edit' : 'fas fa-hand-holding');
    $iconColor = $mode == 'show' ? 'text-info' : 
                 ($mode == 'edit' ? 'text-warning' : 'text-primary');
    $bgColor = $mode == 'show' ? 'bg-info bg-opacity-10' : 
               ($mode == 'edit' ? 'bg-warning bg-opacity-10' : 'bg-primary bg-opacity-10');
@endphp

@extends('layouts.app')

@section('title', $title)
@section('header-icon', $icon)
@section('header-title', $title)

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="{{ $mode == 'show' ? 'col-lg-8' : 'col-lg-8' }}">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="{{ $icon }} {{ $iconColor }} me-2"></i>
                            {{ $title }}
                        </h5>
                        @if($mode == 'show' && isset($borrowing))
                            <span class="badge bg-{{ $borrowing->status_color }} fs-6">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    
                    @if($mode == 'show' && isset($borrowing))
                        <!-- SHOW MODE: Display Borrowing Details -->
                        <div class="row">
                            <!-- Status & Info -->
                            <div class="col-12 mb-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0">
                                            <div class="card-body text-center">
                                                <i class="fas fa-user fa-2x text-primary mb-2"></i>
                                                <h6>Peminjam</h6>
                                                <h5>{{ $borrowing->user->name ?? '-' }}</h5>
                                                <small class="text-muted">{{ $borrowing->user->email ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0">
                                            <div class="card-body text-center">
                                                <i class="fas fa-laptop fa-2x text-success mb-2"></i>
                                                <h6>Alat</h6>
                                                <h5>{{ $borrowing->laptop->nama_alat ?? '-' }}</h5>
                                                <small class="text-muted">#{{ $borrowing->laptop->kode_alat ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0">
                                            <div class="card-body text-center">
                                                <i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
                                                <h6>Durasi</h6>
                                                <h5>{{ $borrowing->days_duration ?? 0 }} hari</h5>
                                                <small class="text-muted">
                                                    {{ $borrowing->tanggal_pinjam->format('d/m/Y') }} - 
                                                    {{ $borrowing->tanggal_kembali->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Borrowing Details -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kode Peminjaman</label>
                                <div class="form-control-plaintext fw-bold">#{{ $borrowing->id }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Status</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-{{ $borrowing->status_color }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                    @if($borrowing->is_overdue)
                                        <span class="badge bg-danger ms-2">Terlambat {{ $borrowing->days_overdue }} hari</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Pinjam</label>
                                <div class="form-control-plaintext">
                                    {{ $borrowing->tanggal_pinjam->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Kembali</label>
                                <div class="form-control-plaintext">
                                    {{ $borrowing->tanggal_kembali->format('d/m/Y H:i') }}
                                    @if($borrowing->is_overdue)
                                        <br><small class="text-danger">(Terlambat)</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Dikembalikan</label>
                                <div class="form-control-plaintext">
                                    {{ $borrowing->tanggal_dikembalikan ? $borrowing->tanggal_dikembalikan->format('d/m/Y H:i') : '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kondisi Kembali</label>
                                <div class="form-control-plaintext">
                                    @if($borrowing->kondisi_kembali)
                                        <span class="badge bg-{{ $borrowing->kondisi_kembali == 'baik' ? 'success' : ($borrowing->kondisi_kembali == 'sedang' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($borrowing->kondisi_kembali) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Keperluan</label>
                                <div class="form-control-plaintext">{{ $borrowing->keperluan ?? '-' }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Catatan</label>
                                <div class="form-control-plaintext">{{ $borrowing->catatan ?? '-' }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Catatan Pengembalian</label>
                                <div class="form-control-plaintext">{{ $borrowing->catatan_kembali ?? '-' }}</div>
                            </div>
                            
                            <!-- Approval Info -->
                            @if($borrowing->approved_at)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Disetujui Oleh</label>
                                <div class="form-control-plaintext">
                                    {{ $borrowing->approver->name ?? '-' }}
                                    <br>
                                    <small class="text-muted">{{ $borrowing->approved_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @endif
                            
                            @if($borrowing->rejected_at)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Ditolak Oleh</label>
                                <div class="form-control-plaintext">
                                    {{ $borrowing->rejector->name ?? '-' }}
                                    <br>
                                    <small class="text-muted">{{ $borrowing->rejected_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Alasan Penolakan</label>
                                <div class="form-control-plaintext">{{ $borrowing->rejection_reason ?? '-' }}</div>
                            </div>
                            @endif
                            
                            <!-- Dates -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Dibuat Pada</label>
                                <div class="form-control-plaintext">{{ $borrowing->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Terakhir Update</label>
                                <div class="form-control-plaintext">{{ $borrowing->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                    @else
                        <!-- CREATE/EDIT MODE: Form Input -->
                        <form method="POST" 
                              action="{{ $mode == 'edit' ? route('admin.borrowings.update', $borrowing) : route('admin.borrowings.store') }}" 
                              id="borrowingForm">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif
                            
                            <!-- Alert Error -->
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Terjadi Kesalahan
                                    </h6>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            <div class="row">
                                <!-- Pilih Peminjam -->
                                <div class="col-md-6 mb-3">
                                    <label for="user_id" class="form-label">
                                        Peminjam <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="user_id" 
                                            class="form-select @error('user_id') is-invalid @enderror" 
                                            required {{ $mode == 'edit' && $borrowing->status != 'pending' ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Peminjam --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ old('user_id', $mode == 'edit' ? $borrowing->user_id : '') == $user->id ? 'selected' : '' }}
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone ?? '-' }}">
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($mode == 'edit' && $borrowing->status != 'pending')
                                        <input type="hidden" name="user_id" value="{{ $borrowing->user_id }}">
                                        <small class="text-muted mt-1">
                                            <i class="fas fa-info-circle"></i> 
                                            Peminjam tidak bisa diubah karena status sudah {{ $borrowing->status }}
                                        </small>
                                    @endif
                                </div>
                                
                                <!-- Informasi Peminjam -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Informasi Peminjam</label>
                                    <div class="p-3 bg-light rounded">
                                        @if($mode == 'edit')
                                            <div class="d-flex align-items-center">
                                                <div class="text-primary me-3">
                                                    <i class="fas fa-user-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $borrowing->user->name ?? '-' }}</div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i> {{ $borrowing->user->email ?? '-' }}<br>
                                                        <i class="fas fa-phone me-1"></i> {{ $borrowing->user->phone ?? '-' }}
                                                    </small>
                                                </div>
                                            </div>
                                        @else
                                            <div id="userInfo" class="d-none">
                                                <div class="d-flex align-items-center">
                                                    <div class="text-primary me-3">
                                                        <i class="fas fa-user-circle fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold" id="selectedUserName">-</div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i> Email: <span id="userEmail">-</span><br>
                                                            <i class="fas fa-phone me-1"></i> Telepon: <span id="userPhone">-</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="noUserInfo" class="text-muted text-center">
                                                <i class="fas fa-user fa-lg mb-2"></i><br>
                                                Pilih peminjam untuk melihat informasi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Pilih Alat -->
                                <div class="col-md-6 mb-3">
                                    <label for="tool_id" class="form-label">
                                        Pilih Alat <span class="text-danger">*</span>
                                    </label>
                                    <select name="tool_id" id="tool_id" 
                                            class="form-select @error('tool_id') is-invalid @enderror" 
                                            required {{ $mode == 'edit' && $borrowing->status != 'pending' ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Alat --</option>
                                        @foreach($laptops as $laptop)
                                            @php
                                                $disabled = false;
                                                if ($mode == 'create' && $laptop->status !== 'tersedia') {
                                                    $disabled = true;
                                                }
                                            @endphp
                                            <option value="{{ $laptop->id }}" 
                                                {{ old('tool_id', $mode == 'edit' ? $borrowing->laptop_id : '') == $laptop->id ? 'selected' : '' }}
                                                data-kode="{{ $laptop->kode_alat }}"
                                                data-nama="{{ $laptop->nama_alat }}"
                                                data-merk="{{ $laptop->merk }}"
                                                data-type="{{ $laptop->type }}"
                                                data-kondisi="{{ $laptop->kondisi }}"
                                                data-lokasi="{{ $laptop->lokasi }}"
                                                {{ $disabled ? 'disabled' : '' }}>
                                                {{ $laptop->nama_alat }} ({{ $laptop->kode_alat }}) 
                                                @if($laptop->status !== 'tersedia')
                                                    - {{ $laptop->status }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tool_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($mode == 'edit' && $borrowing->status != 'pending')
                                        <input type="hidden" name="tool_id" value="{{ $borrowing->laptop_id }}">
                                        <small class="text-muted mt-1">
                                            <i class="fas fa-info-circle"></i> 
                                            Alat tidak bisa diubah karena status sudah {{ $borrowing->status }}
                                        </small>
                                    @endif
                                </div>
                                
                                <!-- Informasi Alat -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Informasi Alat</label>
                                    <div class="p-3 bg-light rounded">
                                        @if($mode == 'edit')
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Kode:</small>
                                                    <strong>{{ $borrowing->laptop->kode_alat ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Merk/Type:</small>
                                                    <strong>{{ $borrowing->laptop->merk ?? '-' }} / {{ $borrowing->laptop->type ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 mt-2">
                                                    <small class="text-muted d-block">Kondisi:</small>
                                                    <span class="badge bg-{{ $borrowing->laptop->kondisi == 'baik' ? 'success' : ($borrowing->laptop->kondisi == 'sedang' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($borrowing->laptop->kondisi ?? '-') }}
                                                    </span>
                                                </div>
                                                <div class="col-6 mt-2">
                                                    <small class="text-muted d-block">Lokasi:</small>
                                                    <strong>{{ $borrowing->laptop->lokasi ?? '-' }}</strong>
                                                </div>
                                            </div>
                                        @else
                                            <div id="toolInfo" class="d-none">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Kode:</small>
                                                        <strong id="toolKode">-</strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Merk/Type:</small>
                                                        <strong id="toolMerk">-</strong>
                                                    </div>
                                                    <div class="col-6 mt-2">
                                                        <small class="text-muted d-block">Kondisi:</small>
                                                        <span id="toolKondisi" class="badge bg-success">-</span>
                                                    </div>
                                                    <div class="col-6 mt-2">
                                                        <small class="text-muted d-block">Lokasi:</small>
                                                        <strong id="toolLokasi">-</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="noToolInfo" class="text-muted text-center">
                                                <i class="fas fa-laptop fa-lg mb-2"></i><br>
                                                Pilih alat untuk melihat detail
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Tanggal Pinjam -->
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pinjam" class="form-label">
                                        Tanggal Pinjam <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" 
                                           name="tanggal_pinjam" 
                                           id="tanggal_pinjam" 
                                           class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                           value="{{ old('tanggal_pinjam', $mode == 'edit' ? $borrowing->tanggal_pinjam->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('tanggal_pinjam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Tanggal Kembali -->
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_kembali" class="form-label">
                                        Tanggal Kembali <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" 
                                           name="tanggal_kembali" 
                                           id="tanggal_kembali" 
                                           class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                           value="{{ old('tanggal_kembali', $mode == 'edit' ? $borrowing->tanggal_kembali->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('tanggal_kembali')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <small class="text-muted">Durasi: <span id="durationText">7 hari</span></small>
                                    </div>
                                </div>
                                
                                <!-- Keperluan -->
                                <div class="col-12 mb-3">
                                    <label for="keperluan" class="form-label">
                                        Keperluan Peminjaman <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="keperluan" 
                                              id="keperluan" 
                                              rows="4" 
                                              class="form-control @error('keperluan') is-invalid @enderror" 
                                              required>{{ old('keperluan', $mode == 'edit' ? $borrowing->keperluan : '') }}</textarea>
                                    @error('keperluan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Catatan -->
                                <div class="col-12 mb-3">
                                    <label for="catatan" class="form-label">Catatan Tambahan</label>
                                    <textarea name="catatan" 
                                              id="catatan" 
                                              rows="3" 
                                              class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $mode == 'edit' ? $borrowing->catatan : '') }}</textarea>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Status (only for edit) -->
                                @if($mode == 'edit')
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">
                                            Status Peminjaman <span class="text-danger">*</span>
                                        </label>
                                        <select name="status" 
                                                id="status" 
                                                class="form-select @error('status') is-invalid @enderror" 
                                                required>
                                            <option value="pending" {{ old('status', $borrowing->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="dipinjam" {{ old('status', $borrowing->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                            <option value="selesai" {{ old('status', $borrowing->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="dibatalkan" {{ old('status', $borrowing->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                            <option value="terlambat" {{ old('status', $borrowing->status) == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                    @endif
                    
                    <!-- Form/Show Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="{{ route('admin.borrowings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        
                        @if($mode == 'show')
                            <div>
                                @if($borrowing->status == 'pending')
                                    <button type="button" class="btn btn-success me-2" onclick="approveBorrowing({{ $borrowing->id }})">
                                        <i class="fas fa-check me-2"></i> Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger me-2" onclick="rejectBorrowing({{ $borrowing->id }})">
                                        <i class="fas fa-times me-2"></i> Tolak
                                    </button>
                                @endif
                                
                                @if($borrowing->status == 'dipinjam')
                                    <button type="button" class="btn btn-primary me-2" onclick="returnTool({{ $borrowing->id }})">
                                        <i class="fas fa-undo me-2"></i> Kembalikan
                                    </button>
                                @endif
                                
                                <a href="{{ route('admin.borrowings.edit', $borrowing) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger" onclick="deleteBorrowing({{ $borrowing->id }}, '#{{ $borrowing->id }}')">
                                    <i class="fas fa-trash me-2"></i> Hapus
                                </button>
                            </div>
                        @else
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                {{ $mode == 'edit' ? 'Update' : 'Simpan' }}
                            </button>
                        @endif
                    </div>
                    
                    @if($mode != 'show')
                        </form>
                    @endif
                </div>
            </div>
            
            @if($mode == 'show' && isset($borrowing))
                <!-- Delete Form (hidden) -->
                <form id="delete-form-{{ $borrowing->id }}" 
                      action="{{ route('admin.borrowings.destroy', $borrowing) }}" 
                      method="POST" 
                      style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                
                <!-- Approve Form (hidden) -->
                <form id="approve-form-{{ $borrowing->id }}" 
                      action="{{ route('admin.borrowings.approve', $borrowing) }}" 
                      method="POST" 
                      style="display: none;">
                    @csrf
                </form>
                
                <!-- Reject Form (hidden) -->
                <form id="reject-form-{{ $borrowing->id }}" 
                      action="{{ route('admin.borrowings.reject', $borrowing) }}" 
                      method="POST" 
                      style="display: none;">
                    @csrf
                </form>
                
                <!-- Return Form (hidden) -->
                <form id="return-form-{{ $borrowing->id }}" 
                      action="{{ route('admin.borrowings.return', $borrowing) }}" 
                      method="POST" 
                      style="display: none;">
                    @csrf
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if($mode == 'create' || $mode == 'edit')
        // Update user info when selected
        document.getElementById('user_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const userInfo = document.getElementById('userInfo');
            const noUserInfo = document.getElementById('noUserInfo');
            
            if (selectedOption.value) {
                userInfo?.classList.remove('d-none');
                noUserInfo?.classList.add('d-none');
                
                const userName = document.getElementById('selectedUserName');
                const userEmail = document.getElementById('userEmail');
                const userPhone = document.getElementById('userPhone');
                
                if (userName) userName.textContent = selectedOption.text.split('(')[0].trim();
                if (userEmail) userEmail.textContent = selectedOption.dataset.email || '-';
                if (userPhone) userPhone.textContent = selectedOption.dataset.phone || '-';
            } else {
                userInfo?.classList.add('d-none');
                noUserInfo?.classList.remove('d-none');
            }
        });
        
        // Update tool info when selected
        document.getElementById('tool_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const toolInfo = document.getElementById('toolInfo');
            const noToolInfo = document.getElementById('noToolInfo');
            
            if (selectedOption.value) {
                toolInfo?.classList.remove('d-none');
                noToolInfo?.classList.add('d-none');
                
                const toolKode = document.getElementById('toolKode');
                const toolMerk = document.getElementById('toolMerk');
                const toolKondisi = document.getElementById('toolKondisi');
                const toolLokasi = document.getElementById('toolLokasi');
                
                if (toolKode) toolKode.textContent = selectedOption.dataset.kode || '-';
                if (toolMerk) toolMerk.textContent = (selectedOption.dataset.merk || '-') + ' / ' + (selectedOption.dataset.type || '-');
                if (toolLokasi) toolLokasi.textContent = selectedOption.dataset.lokasi || '-';
                
                // Update condition badge
                if (toolKondisi) {
                    const kondisi = selectedOption.dataset.kondisi;
                    const conditionColors = {
                        'baik': 'success',
                        'sedang': 'warning',
                        'buruk': 'danger'
                    };
                    const conditionColor = conditionColors[kondisi] || 'secondary';
                    toolKondisi.className = 'badge bg-' + conditionColor;
                    toolKondisi.textContent = kondisi || '-';
                }
            } else {
                toolInfo?.classList.add('d-none');
                noToolInfo?.classList.remove('d-none');
            }
        });
        
        // Calculate duration between dates
        function calculateDuration() {
            const start = document.getElementById('tanggal_pinjam')?.value;
            const end = document.getElementById('tanggal_kembali')?.value;
            const durationText = document.getElementById('durationText');
            
            if (start && end && durationText) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                
                if (endDate > startDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    durationText.textContent = diffDays + ' hari';
                } else {
                    durationText.textContent = 'Tanggal tidak valid';
                }
            }
        }
        
        // Add event listeners for date changes
        document.getElementById('tanggal_pinjam')?.addEventListener('change', calculateDuration);
        document.getElementById('tanggal_kembali')?.addEventListener('change', calculateDuration);
        
        // Form validation
        document.getElementById('borrowingForm')?.addEventListener('submit', function(e) {
            const userId = document.getElementById('user_id')?.value;
            const toolId = document.getElementById('tool_id')?.value;
            const startDate = document.getElementById('tanggal_pinjam')?.value;
            const endDate = document.getElementById('tanggal_kembali')?.value;
            
            if (!userId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Peminjam Belum Dipilih',
                    text: 'Silakan pilih peminjam terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (!toolId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Alat Belum Dipilih',
                    text: 'Silakan pilih alat yang akan dipinjam!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal kembali harus setelah tanggal pinjam!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            return true;
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateDuration();
            // Trigger change events for initial values
            document.getElementById('user_id')?.dispatchEvent(new Event('change'));
            document.getElementById('tool_id')?.dispatchEvent(new Event('change'));
        });
    @endif
    
    // Action functions for show mode
    function approveBorrowing(id) {
        Swal.fire({
            title: 'Setujui Peminjaman?',
            text: "Peminjaman akan disetujui dan alat akan dipinjamkan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`approve-form-${id}`).submit();
            }
        });
    }
    
    function rejectBorrowing(id) {
        Swal.fire({
            title: 'Tolak Peminjaman?',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Harap masukkan alasan penolakan');
                    return false;
                }
                
                // Add reason to form
                const form = document.getElementById(`reject-form-${id}`);
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'rejection_reason';
                input.value = reason;
                form.appendChild(input);
                
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`reject-form-${id}`).submit();
            }
        });
    }
    
    function returnTool(id) {
        Swal.fire({
            title: 'Kembalikan Alat?',
            html: `
                <div class="mb-3">
                    <label class="form-label">Kondisi Alat</label>
                    <select id="kondisi_kembali" class="form-control">
                        <option value="baik">Baik</option>
                        <option value="sedang">Sedang</option>
                        <option value="buruk">Buruk</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea id="catatan_kembali" class="form-control" rows="2" placeholder="Catatan kondisi alat..."></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kembalikan',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const kondisi = document.getElementById('kondisi_kembali').value;
                const catatan = document.getElementById('catatan_kembali').value;
                
                const form = document.getElementById(`return-form-${id}`);
                const kondisiInput = document.createElement('input');
                kondisiInput.type = 'hidden';
                kondisiInput.name = 'kondisi_kembali';
                kondisiInput.value = kondisi;
                form.appendChild(kondisiInput);
                
                const catatanInput = document.createElement('input');
                catatanInput.type = 'hidden';
                catatanInput.name = 'catatan_kembali';
                catatanInput.value = catatan;
                form.appendChild(catatanInput);
                
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`return-form-${id}`).submit();
            }
        });
    }
    
    function deleteBorrowing(id, borrowingCode) {
        Swal.fire({
            title: 'Hapus Peminjaman?',
            html: `Peminjaman ${borrowingCode} akan dihapus permanen!<br><small class="text-danger">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
    
    // Show success/error messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>

<style>
    .form-control-plaintext {
        min-height: 38px;
        padding: 6px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    
    .card.bg-light {
        transition: transform 0.2s;
    }
    
    .card.bg-light:hover {
        transform: translateY(-2px);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 10px;
        }
        
        .d-flex.justify-content-between > div {
            width: 100%;
        }
    }
</style>
@endsection