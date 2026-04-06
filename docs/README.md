# Sistem Peminjaman Laptop - Landing Page

Ini adalah landing page untuk proyek Sistem Peminjaman Laptop.

## Link Penting

- **Repository Utama**: https://github.com/sriyanii/peminjaman-laptop
- **Aplikasi Live**: (Deploy ke Railway/Laravel Cloud)

## Teknologi yang Digunakan

- Laravel 11
- MySQL
- Bootstrap 5
- jQuery & AJAX
- SweetAlert2

## Fitur

- Manajemen User (Admin, Petugas, User)
- Manajemen Laptop (CRUD)
- Peminjaman dengan sistem approve/reject
- Denda otomatis untuk keterlambatan
- Transaksi denda
- Laporan export Excel

## Cara Menjalankan Aplikasi

1. Clone repository
2. Copy `.env.example` ke `.env`
3. Setup database
4. Run `composer install`
5. Run `php artisan key:generate`
6. Run `php artisan migrate --seed`
7. Run `php artisan serve`
