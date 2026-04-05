@extends('layouts.app')

@section('title', $mode == 'show' ? 'Detail User: ' . $user->name : ($mode == 'edit' ? 'Edit User: ' . $user->name : 'Tambah User Baru'))
@section('header-icon', $mode == 'show' ? 'fas fa-user' : ($mode == 'edit' ? 'fas fa-edit' : 'fas fa-user-plus'))
@section('header-title', $mode == 'show' ? 'Detail User: ' . $user->name : ($mode == 'edit' ? 'Edit User' : 'Tambah User Baru'))

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="{{ $mode == 'show' ? 'col-lg-8' : 'col-lg-6' }}">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="{{ $mode == 'show' ? 'fas fa-user' : ($mode == 'edit' ? 'fas fa-edit text-primary' : 'fas fa-user-plus text-primary') }} me-2"></i>
                        @if($mode == 'show')
                            Detail User: {{ $user->name }}
                        @elseif($mode == 'edit')
                            Form Edit User: {{ $user->name }}
                        @else
                            Form Tambah User Baru
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    
                    @if($mode == 'show')
                        <!-- SHOW MODE: Display User Details -->
                        <div class="row">
                            <!-- Profile Info -->
                            <div class="col-12 text-center mb-4">
                                <div class="avatar-circle-lg mx-auto mb-3">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <h4>{{ $user->name }}</h4>
                                <p class="text-muted">{{ $user->email }}</p>
                                
                                <div class="d-flex justify-content-center gap-2 mb-4">
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'petugas' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            <!-- User Details -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Username</label>
                                <div class="form-control-plaintext fw-bold">{{ $user->username }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Email</label>
                                <div class="form-control-plaintext fw-bold">{{ $user->email }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nomor Telepon</label>
                                <div class="form-control-plaintext">{{ $user->phone ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Role</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'petugas' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Alamat</label>
                                <div class="form-control-plaintext">{{ $user->address ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Status</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Akun Aktif</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                        {{ $user->is_active ? 'Ya' : 'Tidak' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Daftar</label>
                                <div class="form-control-plaintext">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Terakhir Update</label>
                                <div class="form-control-plaintext">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                    @else
                        <!-- CREATE/EDIT MODE: Form Input -->
                        <form method="POST" action="{{ $mode == 'edit' ? route('admin.users.update', $user->id) : route('admin.users.store') }}" id="userForm">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif
                            
                            <div class="row">
                                <!-- Nama Lengkap -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name ?? '') }}"
                                           required
                                           placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email ?? '') }}"
                                           required
                                           placeholder="contoh@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username', $user->username ?? '') }}"
                                           required
                                           placeholder="username unik">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        Password 
                                        @if($mode == 'create')
                                            <span class="text-danger">*</span>
                                        @else
                                            <small class="text-muted">(Kosongkan jika tidak diubah)</small>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               {{ $mode == 'create' ? 'required' : '' }}
                                               placeholder="Minimal 8 karakter">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Konfirmasi Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Konfirmasi Password
                                        @if($mode == 'create')<span class="text-danger">*</span>@endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                                               id="password_confirmation" 
                                               name="password_confirmation"
                                               {{ $mode == 'create' ? 'required' : '' }}
                                               placeholder="Ulangi password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone ?? '') }}"
                                           placeholder="0812-3456-7890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" 
                                            id="role" 
                                            name="role" 
                                            required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="petugas" {{ old('role', $user->role ?? '') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                                        <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Address -->
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3"
                                              placeholder="Masukkan alamat lengkap">{{ old('address', $user->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status">
                                        <option value="active" {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $user->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Is Active -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Aktif</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $user->is_active ?? 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Akun Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Form/Show Actions -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            
                            @if($mode == 'show')
                                <div>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
                                        <i class="fas fa-edit me-2"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-trash me-2"></i> Hapus
                                    </button>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
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
            
            @if($mode == 'show')
                <!-- Activity Log -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Aktivitas Terakhir
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Aktivitas</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>Profil diupdate</td>
                                        <td>127.0.0.1</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>Akun dibuat</td>
                                        <td>127.0.0.1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.parentElement.querySelector('button i');
        
        if (field.type === 'password') {
            field.type = 'text';
            button.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            button.className = 'fas fa-eye';
        }
    }
    
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userForm');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password')?.value;
                const passwordConfirmation = document.getElementById('password_confirmation')?.value;
                const isEditMode = {{ $mode == 'edit' ? 'true' : 'false' }};
                
                // For new user, password is required
                if (!isEditMode && password && password.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password terlalu pendek',
                        text: 'Password minimal 8 karakter'
                    });
                    return false;
                }
                
                // Check password match
                if (password && passwordConfirmation && password !== passwordConfirmation) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password tidak cocok',
                        text: 'Password dan konfirmasi password harus sama'
                    });
                    return false;
                }
                
                return true;
            });
        }
    });
    
    // Confirm delete
    function confirmDelete(userId, userName) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `User <strong>${userName}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${userId}`).submit();
            }
        });
    }
</script>

<style>
    .avatar-circle-lg {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #3a86ff, #2667cc);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: bold;
        margin: 0 auto 20px;
        overflow: hidden;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .form-control-plaintext {
        min-height: 38px;
        padding: 6px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    
    .form-check-input:checked {
        background-color: #3a86ff;
        border-color: #3a86ff;
    }
</style>
@endsection