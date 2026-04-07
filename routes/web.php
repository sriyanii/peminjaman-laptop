<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;

use App\Http\Controllers\Petugas\TransaksiDendaController;
use App\Http\Controllers\Petugas\PeminjamanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/', fn() => view('auth.login'))->name('welcome');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION
    |--------------------------------------------------------------------------
    */

    Route::prefix('email')->name('verification.')->group(function () {

        Route::get('/verify', [VerificationController::class, 'show'])->name('notice');

        Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verify');

        Route::post('/resend', [VerificationController::class, 'resend'])->name('resend');

    });

    /*
    |--------------------------------------------------------------------------
    | CONFIRM PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'adminDashboard'])->name('dashboard');

            Route::resource('users', App\Http\Controllers\UserController::class);
            Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
            
            Route::resource('laptops', App\Http\Controllers\Admin\LaptopController::class);
            Route::get('/laptops/{id}/detail', [App\Http\Controllers\Admin\LaptopController::class, 'getDetail'])->name('laptops.detail');

            Route::resource('borrowings', App\Http\Controllers\Admin\BorrowingController::class);
            
            
            Route::prefix('borrowings')->name('borrowings.')->group(function () {
                Route::get('/get-kode/{id}', [App\Http\Controllers\Admin\BorrowingController::class, 'getKodePeminjaman'])->name('get-kode');
                Route::get('/get-detail/{id}', [App\Http\Controllers\Admin\BorrowingController::class, 'getDetail'])->name('get-detail');
                Route::put('/update-status/{id}', [App\Http\Controllers\Admin\BorrowingController::class, 'updateStatus'])->name('update-status');
                Route::put('/return/{id}', [App\Http\Controllers\Admin\BorrowingController::class, 'return'])->name('return');
                
            });

            Route::prefix('transactions')->name('transactions.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('show');
                Route::delete('/{id}', [App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->name('destroy');
                Route::post('/export', [App\Http\Controllers\Admin\TransactionController::class, 'export'])->name('export');
            });

        Route::get('/laporan', [App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [App\Http\Controllers\Admin\LaporanController::class, 'export'])->name('laporan.export');

    });


    /*
    |--------------------------------------------------------------------------
    | PETUGAS ROUTES
    |--------------------------------------------------------------------------
    */

Route::middleware('petugas')
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Petugas\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('alat', App\Http\Controllers\Petugas\AlatController::class)->except(['show']);
        Route::get('/alat/{id}/detail', [App\Http\Controllers\Petugas\AlatController::class, 'getDetail'])->name('alat.detail');

        // Peminjaman routes
        Route::resource('peminjaman', App\Http\Controllers\Petugas\PeminjamanController::class);
    Route::post('/peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('petugas.peminjaman.approve');
    Route::post('/peminjaman/{id}/reject', [PeminjamanController::class, 'reject'])->name('petugas.peminjaman.reject');
    Route::post('/peminjaman/{id}/pickup', [PeminjamanController::class, 'confirmPickup'])->name('petugas.peminjaman.pickup');
    Route::post('/peminjaman/{id}/transaksi', [PeminjamanController::class, 'prosesTransaksi'])->name('petugas.peminjaman.transaksi');
        
       Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'show'])->name('show');
            Route::get('/{id}/data', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'getData'])->name('data');
            Route::post('/{id}/bayar', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'bayar'])->name('bayar');  // ← Pastikan ini ada
            Route::get('/{id}/cetak', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'cetakStruk'])->name('cetak');
        
            
            // ✅ TAMBAHKAN DUA ROUTE INI:
            Route::get('/{id}/bayar-form', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'formBayar'])->name('bayar.form');
            Route::post('/{id}/proses-bayar', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'prosesBayar'])->name('bayar.proses');
            
            Route::get('/{id}/cetak', [App\Http\Controllers\Petugas\TransaksiDendaController::class, 'cetakStruk'])->name('cetak');
        });

        Route::get('/laporan', [App\Http\Controllers\Petugas\LaporanController::class, 'index'])->name('laporan');
    });

    /*
    |--------------------------------------------------------------------------
    | USER ROUTES
    |--------------------------------------------------------------------------
    */

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:user,peminjam'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/alat', [App\Http\Controllers\User\AlatController::class, 'index'])->name('alat');

        Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\PeminjamanController::class, 'index'])->name('index');
            Route::get('/create/{laptop_id?}', [App\Http\Controllers\User\PeminjamanController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\User\PeminjamanController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\User\PeminjamanController::class, 'show'])->name('show');
            Route::delete('/{id}', [App\Http\Controllers\User\PeminjamanController::class, 'destroy'])->name('destroy');
            
            // ✅ ROUTE UNTUK HAPUS PERMANEN (selesai/ditolak/batal)
            Route::delete('/{id}/force', [App\Http\Controllers\User\PeminjamanController::class, 'forceDelete'])->name('force-delete');
            
            Route::post('/{id}/take', [App\Http\Controllers\User\PeminjamanController::class, 'takeItem'])->name('take');
        });

        Route::prefix('pengembalian')->name('pengembalian.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\PengembalianController::class, 'index'])->name('index');
            Route::get('/create/{peminjaman_id?}', [App\Http\Controllers\User\PengembalianController::class, 'create'])->name('create');
            Route::post('/{peminjaman}', [App\Http\Controllers\User\PengembalianController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\User\PengembalianController::class, 'show'])->name('show');
        });

        Route::get('/riwayat', [App\Http\Controllers\User\RiwayatController::class, 'index'])->name('history');

    });
    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')->name('profile.')->group(function () {

        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');

    });

    /*
    |--------------------------------------------------------------------------
    | PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::prefix('password')->name('password.')->group(function () {

        Route::get('/edit', [PasswordController::class, 'edit'])->name('edit');
        Route::put('/update', [PasswordController::class, 'update'])->name('change');

    });

});

Route::get('/test-session', function() {
    session(['test' => 'value']);
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'user' => auth()->user() ? auth()->user()->id : null,
        'session_path' => session_save_path()
    ]);
})->middleware('auth');