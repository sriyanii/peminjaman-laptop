@php
    $isEdit = isset($transaction);
    $title = $isEdit ? 'Edit Transaksi #' . $transaction->kode_transaksi : 'Buat Transaksi Baru';
    $action = $isEdit ? route('admin.transactions.update', $transaction) : route('admin.transactions.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

@extends('layouts.app')

@section('title', $title)
@section('header-icon', $isEdit ? 'fas fa-edit' : 'fas fa-money-bill-wave')
@section('header-title', $title)


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="{{ $isEdit ? 'fas fa-edit text-warning' : 'fas fa-plus text-primary' }} me-2"></i>
                            {{ $title }}
                        </h5>
                        @if($isEdit)
                            <span class="badge bg-{{ $transaction->status_color }} fs-6">
                                {{ $transaction->status_text }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ $action }}" method="POST" id="transactionForm" enctype="multipart/form-data">
                        @csrf
                        @if($isEdit) @method($method) @endif
                        
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
                            <!-- Pilih User -->
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label fw-semibold">
                                    User <span class="text-danger">*</span>
                                </label>
                                <select name="user_id" id="user_id" 
                                        class="form-select @error('user_id') is-invalid @enderror" 
                                        required {{ $isEdit ? 'disabled' : '' }}>
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                            {{ old('user_id', $isEdit ? $transaction->user_id : '') == $user->id ? 'selected' : '' }}
                                            data-saldo="{{ $user->saldo }}">
                                            {{ $user->name }} ({{ $user->email }}) - Saldo: {{ $user->saldo_formatted }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($isEdit)
                                    <input type="hidden" name="user_id" value="{{ $transaction->user_id }}">
                                @endif
                            </div>
                            
                            <!-- Informasi User -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Informasi User</label>
                                <div class="p-3 bg-light rounded">
                                    @if($isEdit)
                                        <div class="d-flex align-items-center">
                                            <div class="text-primary me-3">
                                                <i class="fas fa-user-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transaction->user->name ?? '-' }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i> {{ $transaction->user->email ?? '-' }}<br>
                                                    <i class="fas fa-wallet me-1"></i> Saldo: {{ $transaction->user->saldo_formatted ?? 'Rp 0' }}
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
                                                        <i class="fas fa-wallet me-1"></i> Saldo: <span id="userSaldo">Rp 0</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="noUserInfo" class="text-muted text-center">
                                            <i class="fas fa-user fa-lg mb-2"></i><br>
                                            Pilih user untuk melihat informasi
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Jenis Transaksi -->
                            <div class="col-md-6 mb-3">
                                <label for="jenis_transaksi" class="form-label fw-semibold">
                                    Jenis Transaksi <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_transaksi" id="jenis_transaksi" 
                                        class="form-select @error('jenis_transaksi') is-invalid @enderror" 
                                        required {{ $isEdit ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="deposit" {{ old('jenis_transaksi', $isEdit ? $transaction->jenis_transaksi : '') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                    <option value="withdraw" {{ old('jenis_transaksi', $isEdit ? $transaction->jenis_transaksi : '') == 'withdraw' ? 'selected' : '' }}>Penarikan</option>
                                    <option value="payment" {{ old('jenis_transaksi', $isEdit ? $transaction->jenis_transaksi : '') == 'payment' ? 'selected' : '' }}>Pembayaran</option>
                                    <option value="refund" {{ old('jenis_transaksi', $isEdit ? $transaction->jenis_transaksi : '') == 'refund' ? 'selected' : '' }}>Pengembalian Dana</option>
                                    <option value="penalty" {{ old('jenis_transaksi', $isEdit ? $transaction->jenis_transaksi : '') == 'penalty' ? 'selected' : '' }}>Denda</option>
                                </select>
                                @error('jenis_transaksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($isEdit)
                                    <input type="hidden" name="jenis_transaksi" value="{{ $transaction->jenis_transaksi }}">
                                @endif
                            </div>
                            
                            <!-- Peminjaman (Optional) -->
                            <div class="col-md-6 mb-3">
                                <label for="borrowing_id" class="form-label fw-semibold">
                                    Peminjaman (Opsional)
                                </label>
                                <select name="borrowing_id" id="borrowing_id" 
                                        class="form-select @error('borrowing_id') is-invalid @enderror">
                                    <option value="">-- Pilih Peminjaman --</option>
                                    @foreach($borrowings as $borrowing)
                                        <option value="{{ $borrowing->id }}" 
                                            {{ old('borrowing_id', $isEdit ? $transaction->borrowing_id : '') == $borrowing->id ? 'selected' : '' }}>
                                            #{{ $borrowing->id }} - {{ $borrowing->user->name ?? '-' }} - {{ $borrowing->laptop->nama_alat ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('borrowing_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Jumlah -->
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label fw-semibold">
                                    Jumlah <span class="text-danger">*</span>
                                    <small class="text-muted">(minimal Rp 1.000)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           name="jumlah" 
                                           id="jumlah" 
                                           class="form-control @error('jumlah') is-invalid @enderror"
                                           value="{{ old('jumlah', $isEdit ? $transaction->jumlah : '') }}"
                                           min="1000" 
                                           step="1000"
                                           required>
                                </div>
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <small class="text-muted" id="amountWarning"></small>
                                </div>
                            </div>
                            
                            <!-- Metode Pembayaran -->
                            <div class="col-md-6 mb-3" id="metodePembayaranContainer">
                                <label for="metode_pembayaran" class="form-label fw-semibold">
                                    Metode Pembayaran <span id="metodeRequired" class="text-danger">*</span>
                                </label>
                                <select name="metode_pembayaran" id="metode_pembayaran" 
                                        class="form-select @error('metode_pembayaran') is-invalid @enderror">
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="cash" {{ old('metode_pembayaran', $isEdit ? $transaction->metode_pembayaran : '') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                    <option value="transfer" {{ old('metode_pembayaran', $isEdit ? $transaction->metode_pembayaran : '') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="e-wallet" {{ old('metode_pembayaran', $isEdit ? $transaction->metode_pembayaran : '') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Keterangan -->
                            <div class="col-12 mb-3">
                                <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                                <textarea name="keterangan" 
                                          id="keterangan" 
                                          rows="3" 
                                          class="form-control @error('keterangan') is-invalid @enderror"
                                          placeholder="Deskripsi transaksi...">{{ old('keterangan', $isEdit ? $transaction->keterangan : '') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Bukti Pembayaran -->
                            <div class="col-md-6 mb-3">
                                <label for="bukti_pembayaran" class="form-label fw-semibold">
                                    Bukti Pembayaran
                                    <small class="text-muted">(jpg, png, max 2MB)</small>
                                </label>
                                <input type="file" 
                                       name="bukti_pembayaran" 
                                       id="bukti_pembayaran" 
                                       class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                                       accept="image/*">
                                @error('bukti_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($isEdit && $transaction->bukti_pembayaran)
                                    <div class="mt-2">
                                        <small class="text-muted">Bukti saat ini:</small>
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/' . $transaction->bukti_pembayaran) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye me-1"></i> Lihat Bukti
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Preview Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Preview Gambar</label>
                                <div class="border rounded p-3 text-center" id="imagePreviewContainer">
                                    @if($isEdit && $transaction->bukti_pembayaran)
                                        <img src="{{ asset('storage/' . $transaction->bukti_pembayaran) }}" 
                                             alt="Bukti Pembayaran" 
                                             id="imagePreview"
                                             class="img-fluid rounded" 
                                             style="max-height: 150px;">
                                    @else
                                        <div class="text-muted">
                                            <i class="fas fa-image fa-2x mb-2"></i><br>
                                            Preview akan muncul di sini
                                        </div>
                                        <img src="" alt="Preview" id="imagePreview" class="img-fluid rounded d-none" style="max-height: 150px;">
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Catatan Admin (Edit only) -->
                            @if($isEdit && $transaction->catatan_admin)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Catatan Admin</label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $transaction->catatan_admin }}
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Catatan Admin Input (Edit only) -->
                            @if($isEdit)
                                <div class="col-12 mb-3">
                                    <label for="catatan_admin" class="form-label fw-semibold">Catatan Admin (Opsional)</label>
                                    <textarea name="catatan_admin" 
                                              id="catatan_admin" 
                                              rows="2" 
                                              class="form-control @error('catatan_admin') is-invalid @enderror"
                                              placeholder="Catatan untuk transaksi ini...">{{ old('catatan_admin', $transaction->catatan_admin) }}</textarea>
                                    @error('catatan_admin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        
                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            
                            <div class="d-flex gap-2">
                                @if($isEdit && $transaction->status === 'pending')
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteTransaction({{ $transaction->id }}, '{{ $transaction->kode_transaksi }}')">
                                        <i class="fas fa-trash me-2"></i> Hapus
                                    </button>
                                @endif
                                
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>
                                    {{ $isEdit ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id');
        const jenisSelect = document.getElementById('jenis_transaksi');
        const jumlahInput = document.getElementById('jumlah');
        const metodeContainer = document.getElementById('metodePembayaranContainer');
        const metodeRequired = document.getElementById('metodeRequired');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const buktiInput = document.getElementById('bukti_pembayaran');
        
        // Update user info when selected
        userSelect?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const userInfo = document.getElementById('userInfo');
            const noUserInfo = document.getElementById('noUserInfo');
            
            if (selectedOption.value) {
                userInfo?.classList.remove('d-none');
                noUserInfo?.classList.add('d-none');
                
                const userName = document.getElementById('selectedUserName');
                const userEmail = document.getElementById('userEmail');
                const userSaldo = document.getElementById('userSaldo');
                
                if (userName) userName.textContent = selectedOption.text.split('(')[0].trim();
                if (userEmail) userEmail.textContent = selectedOption.dataset.email || '-';
                if (userSaldo) {
                    const saldo = selectedOption.dataset.saldo || 0;
                    userSaldo.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldo);
                }
                
                // Validate amount based on user balance
                validateAmount();
            } else {
                userInfo?.classList.add('d-none');
                noUserInfo?.classList.remove('d-none');
            }
        });
        
        // Show/hide metode pembayaran based on jenis transaksi
        jenisSelect?.addEventListener('change', function() {
            const jenis = this.value;
            
            if (jenis === 'deposit') {
                metodeContainer.style.display = 'block';
                metodeRequired.style.display = 'inline';
            } else {
                metodeContainer.style.display = 'block';
                metodeRequired.style.display = 'none';
            }
            
            // Validate amount
            validateAmount();
        });
        
        // Validate amount based on jenis and user balance
        function validateAmount() {
            const jenis = jenisSelect?.value;
            const amount = parseFloat(jumlahInput?.value) || 0;
            const userSaldo = parseFloat(userSelect?.options[userSelect.selectedIndex]?.dataset.saldo) || 0;
            const warningElement = document.getElementById('amountWarning');
            
            if (!warningElement) return;
            
            if (jenis === 'withdraw' || jenis === 'payment' || jenis === 'penalty') {
                if (amount > userSaldo) {
                    warningElement.innerHTML = `<span class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Saldo tidak mencukupi! Saldo tersedia: Rp ${new Intl.NumberFormat('id-ID').format(userSaldo)}
                    </span>`;
                } else {
                    warningElement.innerHTML = `<span class="text-success">
                        <i class="fas fa-check-circle"></i> 
                        Saldo mencukupi
                    </span>`;
                }
            } else if (jenis === 'deposit' || jenis === 'refund') {
                warningElement.innerHTML = `<span class="text-info">
                    <i class="fas fa-info-circle"></i> 
                    Saldo akan bertambah
                </span>`;
            } else {
                warningElement.innerHTML = '';
            }
        }
        
        // Validate on amount change
        jumlahInput?.addEventListener('input', validateAmount);
        
        // Image preview
        buktiInput?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = imagePreview;
                    const placeholder = imagePreviewContainer.querySelector('.text-muted');
                    
                    if (placeholder) placeholder.style.display = 'none';
                    imgElement.src = e.target.result;
                    imgElement.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation
        document.getElementById('transactionForm')?.addEventListener('submit', function(e) {
            const jenis = jenisSelect?.value;
            const userId = userSelect?.value;
            const amount = parseFloat(jumlahInput?.value) || 0;
            const userSaldo = parseFloat(userSelect?.options[userSelect.selectedIndex]?.dataset.saldo) || 0;
            const metode = document.getElementById('metode_pembayaran')?.value;
            
            if (!userId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'User Belum Dipilih',
                    text: 'Silakan pilih user terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (!jenis) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Jenis Transaksi Belum Dipilih',
                    text: 'Silakan pilih jenis transaksi!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (amount < 1000) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Jumlah Tidak Valid',
                    text: 'Jumlah minimal Rp 1.000!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (jenis === 'deposit' && !metode) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Metode Pembayaran Belum Dipilih',
                    text: 'Silakan pilih metode pembayaran untuk deposit!',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if ((jenis === 'withdraw' || jenis === 'payment' || jenis === 'penalty') && amount > userSaldo) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Saldo Tidak Mencukupi',
                    html: `Jumlah melebihi saldo yang tersedia!<br>
                          <strong>Jumlah:</strong> Rp ${new Intl.NumberFormat('id-ID').format(amount)}<br>
                          <strong>Saldo Tersedia:</strong> Rp ${new Intl.NumberFormat('id-ID').format(userSaldo)}`,
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
                submitBtn.disabled = true;
            }
            
            return true;
        });
        
        // Initialize
        if (jenisSelect) jenisSelect.dispatchEvent(new Event('change'));
        if (userSelect) userSelect.dispatchEvent(new Event('change'));
    });
    
    // Delete transaction
    function deleteTransaction(id, kode) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Transaksi <strong>${kode}</strong> akan dihapus permanen!<br>
                  <small class="text-danger">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/transactions/${id}`;
                form.style.display = 'none';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // Notifikasi dari session
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


@endsection