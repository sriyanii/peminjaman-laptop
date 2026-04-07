<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Peminjaman Laptop')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @push('styles')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush
    @stack('styles')
</head>
<body>
    @if(auth()->check())
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-laptop"></i>
                <h3>Sistem Peminjaman Laptop</h3>
            </div>
            
            <div class="sidebar-nav-container">
                @if(auth()->user()->role === 'admin')
                    <!-- MENU ADMIN -->
                    <div class="menu-section">
                        <div class="menu-section-title">Menu Admin</div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" 
                                   href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.laptops*') ? 'active' : '' }}" 
                                   href="{{ route('admin.laptops.index') }}">
                                    <i class="fas fa-laptop"></i> Laptops
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" 
                                   href="{{ route('admin.categories.index') }}">
                                    <i class="fas fa-tags"></i> Kategori
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.borrowings*') ? 'active' : '' }}" 
                                   href="{{ route('admin.borrowings.index') }}">
                                    <i class="fas fa-hand-holding"></i> Peminjaman
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}" 
                                   href="{{ route('admin.transactions.index') }}">
                                    <i class="fas fa-money-bill-wave"></i> Transaksi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}" 
                                   href="{{ route('admin.laporan.index') }}">
                                    <i class="fas fa-chart-bar"></i> Laporan
                                </a>
                            </li>
                        </ul>
                    </div>
                @elseif(auth()->user()->role === 'petugas')
                    <!-- MENU PETUGAS -->
                    <div class="menu-section">
                        <div class="menu-section-title">Menu Petugas</div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('petugas.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.peminjaman*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.peminjaman.index') }}">
                                    <i class="fas fa-hand-holding"></i> Peminjaman
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.transaksi*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.transaksi.index') }}">
                                    <i class="fas fa-money-bill-wave"></i> Transaksi & Denda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.alat*') ? 'active' : '' }}" 
                                   href="{{ route('petugas.alat.index') }}">
                                    <i class="fas fa-laptop"></i> Data Laptop
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('petugas.laporan') ? 'active' : '' }}" 
                                   href="{{ route('petugas.laporan') }}">
                                    <i class="fas fa-chart-bar"></i> Laporan
                                </a>
                            </li>
                        </ul>
                    </div>
                @elseif(auth()->user()->role === 'user' || auth()->user()->role === 'peminjam')
                    <!-- MENU USER -->
                    <div class="menu-section">
                        <div class="menu-section-title">Menu Peminjam</div>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('user.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user.alat') ? 'active' : '' }}" 
                                   href="{{ route('user.alat') }}">
                                    <i class="fas fa-laptop"></i> Daftar Laptop
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user.peminjaman*') ? 'active' : '' }}" 
                                   href="{{ route('user.peminjaman.index') }}">
                                    <i class="fas fa-hand-holding"></i> Peminjaman Saya
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
                
                <!-- MENU AKUN -->
                <div class="menu-section">
                    <div class="menu-section-title">Akun</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <a href="#" class="nav-link" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <i class="fas fa-copyright me-1"></i> {{ date('Y') }} Sistem Peminjaman Laptop
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <div class="content-container">
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </main>
        
        <button class="btn btn-primary" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
    @else
        <div class="login-page w-100">
            @yield('content')
        </div>
    @endif

    {{-- Toast Notification Container --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <i class="fas fa-circle-info me-2" id="toastIcon"></i>
                <strong class="me-auto" id="toastTitle">Notifikasi</strong>
                <small>Baru saja</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Pesan notifikasi
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- ✅ TAMBAHKAN SweetAlert2 DI SINI -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Function to show toast notification
        function showToast(message, type = 'success', title = null) {
            const toastEl = document.getElementById('liveToast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            
            switch(type) {
                case 'success':
                    toastIcon.className = 'fas fa-check-circle me-2 text-success';
                    toastTitle.innerHTML = title || 'Berhasil!';
                    break;
                case 'error':
                    toastIcon.className = 'fas fa-exclamation-circle me-2 text-danger';
                    toastTitle.innerHTML = title || 'Gagal!';
                    break;
                case 'warning':
                    toastIcon.className = 'fas fa-exclamation-triangle me-2 text-warning';
                    toastTitle.innerHTML = title || 'Peringatan!';
                    break;
                case 'info':
                    toastIcon.className = 'fas fa-info-circle me-2 text-info';
                    toastTitle.innerHTML = title || 'Informasi';
                    break;
                default:
                    toastIcon.className = 'fas fa-circle-info me-2';
                    toastTitle.innerHTML = title || 'Notifikasi';
            }
            
            toastMessage.innerHTML = message;
            
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        }

        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif

        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif

        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
        
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.className = sidebar.classList.contains('active') ? 'fas fa-times' : 'fas fa-bars';
        });
        
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    document.getElementById('sidebar').classList.remove('active');
                    document.getElementById('sidebarToggle').querySelector('i').className = 'fas fa-bars';
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>