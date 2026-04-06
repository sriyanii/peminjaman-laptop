@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('header-icon', 'fas fa-hand-holding')
@section('header-title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Detail Peminjaman
                </h5>
                <a href="{{ route('admin.borrowings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-user me-2"></i>Informasi Peminjam</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Nama Lengkap</th>
                                    <td>{{ $borrowing->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $borrowing->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor Telepon</th>
                                    <td>{{ $borrowing->user->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NIM/NIK</th>
                                    <td>{{ $borrowing->user->nim ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>{{ ucfirst($borrowing->user->role ?? '-') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-laptop me-2"></i>Informasi Laptop</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="35%">Merk & Model</th>
                                    <td>{{ $borrowing->laptop->merk ?? '-' }} {{ $borrowing->laptop->model ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Serial Number</th>
                                    <td>{{ $borrowing->laptop->serial_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kode Alat</th>
                                    <td>{{ $borrowing->laptop->kode_alat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kondisi</th>
                                    <td>{{ $borrowing->laptop->kondisi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status Laptop</th>
                                    <td>
                                        @if($borrowing->laptop)
                                            <span class="badge bg-{{ $borrowing->laptop->status == 'tersedia' ? 'success' : 'warning' }}">
                                                {{ ucfirst($borrowing->laptop->status) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong><i class="fas fa-calendar-alt me-2"></i>Informasi Peminjaman</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Kode Peminjaman</th>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $borrowing->kode_peminjaman ?? 'PINJ-' . date('Ymd', strtotime($borrowing->created_at)) . '-' . str_pad($borrowing->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pinjam</th>
                                    <td>{{ \Carbon\Carbon::parse($borrowing->tanggal_pinjam)->translatedFormat('l, d F Y') }}<br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($borrowing->tanggal_pinjam)->format('H:i') }} WIB</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Rencana Kembali</th>
                                    <td>{{ \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana)->translatedFormat('l, d F Y') }}<br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana)->format('H:i') }} WIB</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Tanggal Kembali</th>
                                    <td>
                                        @if($borrowing->tanggal_kembali)
                                            {{ \Carbon\Carbon::parse($borrowing->tanggal_kembali)->translatedFormat('l, d F Y') }}<br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($borrowing->tanggal_kembali)->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-muted">Belum dikembalikan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'aktif' => 'primary',
                                                'selesai' => 'success',
                                                'ditolak' => 'danger',
                                                'batal' => 'secondary'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'aktif' => 'Dipinjam',
                                                'selesai' => 'Selesai',
                                                'ditolak' => 'Ditolak',
                                                'batal' => 'Batal'
                                            ];
                                            $isTerlambat = in_array($borrowing->status, ['approved', 'aktif']) && 
                                                           $borrowing->tanggal_kembali_rencana && 
                                                           \Carbon\Carbon::parse($borrowing->tanggal_kembali_rencana)->isPast() &&
                                                           !$borrowing->tanggal_kembali;
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$borrowing->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$borrowing->status] ?? ucfirst($borrowing->status) }}
                                        </span>
                                        @if($isTerlambat)
                                            <span class="badge bg-danger ms-1">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Denda</th>
                                    <td>
                                        @if($borrowing->denda > 0)
                                            <span class="text-danger fw-bold">Rp {{ number_format($borrowing->denda, 0, ',', '.') }}</span>
                                            @if(!($borrowing->is_denda_dibayar ?? false))
                                                <span class="badge bg-warning ms-1">Belum Dibayar</span>
                                            @else
                                                <span class="badge bg-success ms-1">Lunas</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Tidak ada denda</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($borrowing->tujuan || $borrowing->keterangan)
                    <div class="row mt-3">
                        <div class="col-12">
                            <table class="table table-sm table-borderless">
                                @if($borrowing->tujuan)
                                <tr>
                                    <th width="15%">Tujuan</th>
                                    <td>
                                        @php
                                            $tujuanLabels = [
                                                'meeting' => 'Meeting',
                                                'presentasi' => 'Presentasi',
                                                'training' => 'Training',
                                                'work_from_home' => 'Work From Home',
                                                'proyek' => 'Proyek',
                                                'lainnya' => 'Lainnya'
                                            ];
                                        @endphp
                                        {{ $tujuanLabels[$borrowing->tujuan] ?? $borrowing->tujuan }}
                                    </td>
                                </tr>
                                @endif
                                @if($borrowing->keterangan)
                                <tr>
                                    <th width="15%">Keterangan</th>
                                    <td>{{ $borrowing->keterangan }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if($borrowing->alasan_ditolak)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-danger mb-0">
                                <strong><i class="fas fa-ban me-2"></i>Alasan Ditolak:</strong><br>
                                {{ $borrowing->alasan_ditolak }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($borrowing->catatan_pengembalian)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <strong><i class="fas fa-sticky-note me-2"></i>Catatan Pengembalian:</strong><br>
                                {{ $borrowing->catatan_pengembalian }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($borrowing->approved_by || $borrowing->waktu_approve)
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong><i class="fas fa-check-circle me-2"></i>Informasi Persetujuan</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Disetujui/Ditolak Oleh</th>
                                    <td>{{ $borrowing->approver->name ?? '-' }}<br>
                                        <small class="text-muted">{{ $borrowing->approver->email ?? '-' }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Waktu Persetujuan</th>
                                    <td>{{ $borrowing->waktu_approve ? \Carbon\Carbon::parse($borrowing->waktu_approve)->translatedFormat('l, d F Y H:i') . ' WIB' : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Setujui -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Persetujuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                    <div class="mb-3">
                        <label for="catatan_approve" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan_approve" name="catatan" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>Konfirmasi Penolakan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
                    <div class="mb-3">
                        <label for="alasan_tolak" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_tolak" name="alasan_ditolak" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kembalikan -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>Konfirmasi Pengembalian
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="returnForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mengembalikan laptop ini?</p>
                    <div class="mb-3">
                        <label for="tanggal_kembali" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="kondisi" class="form-label">Kondisi Laptop <span class="text-danger">*</span></label>
                        <select class="form-select" id="kondisi" name="kondisi" required>
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_return" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan_return" name="catatan" rows="2" placeholder="Catatan kondisi atau lainnya..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function approveBorrowing(id) {
        const form = document.getElementById('approveForm');
        form.action = '{{ url("/admin/borrowings/update-status") }}/' + id;
        const modal = new bootstrap.Modal(document.getElementById('approveModal'));
        modal.show();
    }
    
    function rejectBorrowing(id) {
        const form = document.getElementById('rejectForm');
        form.action = '{{ url("/admin/borrowings/update-status") }}/' + id;
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }
    
    function returnTool(id) {
        const form = document.getElementById('returnForm');
        form.action = '{{ url("/admin/borrowings/return") }}/' + id;
        const modal = new bootstrap.Modal(document.getElementById('returnModal'));
        modal.show();
    }
    
    function deleteBorrowing(id) {
        Swal.fire({
            title: 'Hapus Peminjaman?',
            text: "Data peminjaman akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url("/admin/borrowings") }}/' + id;
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