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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        :root {
            --primary: #3a86ff;
            --secondary: #2667cc;
            --dark: #1f2937;
            --light: #f9fafb;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --sidebar-bg: linear-gradient(180deg, #1e3a8a 0%, #3b82f6 100%);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: var(--dark);
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
        }
        
        /* SIDEBAR */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .sidebar.active { transform: translateX(0); }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
        }
        
        .sidebar-brand {
            padding: 20px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-brand i {
            font-size: 40px;
            color: white;
            margin-bottom: 10px;
            display: block;
        }
        
        .sidebar-brand h3 {
            color: white;
            font-weight: 600;
            margin: 0;
            font-size: 22px;
        }
        
        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            padding: 15px 0;
        }
        
        .menu-section {
            margin-bottom: 25px;
            padding: 0 15px;
        }
        
        .menu-section-title {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 10px;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .nav { padding: 0 10px; }
        .nav-item { margin-bottom: 5px; }
        
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            padding: 12px 15px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
            text-align: center;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: white !important;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: white !important;
            color: var(--primary) !important;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .sidebar-footer {
            padding: 20px 25px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.1);
            color: rgba(255,255,255,0.6);
            font-size: 12px;
        }
        
        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
            overflow-y: auto;
            background: #f8fafc;
        }
        
        @media (max-width: 992px) {
            .main-content { margin-left: 0; width: 100%; }
        }
        
        .content-container {
            padding: 25px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header h2 {
            color: var(--dark);
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .header h2 i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
        }
        
        .role-badge {
            background: var(--primary);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
        }
        
        .content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            min-height: 500px;
        }
        
        /* STATS CARD */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            height: 100%;
            transition: transform 0.3s ease;
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
            color: var(--dark);
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* MOBILE TOGGLE */
        #sidebarToggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1001;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 992px) {
            #sidebarToggle { display: flex; }
        }
        
        @media (max-width: 768px) {
            .sidebar { width: 260px; }
            .content-container { padding: 15px; }
            .header { padding: 15px 20px; }
            .content { padding: 20px; }
            .stats-number { font-size: 24px; }
        }
    </style>
    
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
                            <!-- <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('user.pengembalian*') ? 'active' : '' }}" 
                                   href="{{ route('user.pengembalian.index') }}">
                                    <i class="fas fa-undo-alt"></i> Pengembalian
                                </a>
                            </li> -->
                        </ul>
                    </div>
                @endif
                
                <!-- MENU AKUN -->
                <div class="menu-section">
                    <div class="menu-section-title">Akun</div>
                    <ul class="nav flex-column">
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.index') }}">
                                <i class="fas fa-user"></i> Profil
                            </a>
                        </li> -->
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
    
    <script>
        // Function to show toast notification
function showToast(message, type = 'success', title = null) {
    const toastEl = document.getElementById('liveToast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    // Set icon and title based on type
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

// Show session messages as toasts
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