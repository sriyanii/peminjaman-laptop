<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;


use App\Http\Controllers\Petugas\TransaksiDendaController;
use App\Http\Controllers\Petugas\PengembalianController;

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
        Route::resource('laptops', App\Http\Controllers\Admin\LaptopController::class);
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

        /*
        | Borrowings
        */

        Route::prefix('borrowings')->name('borrowings.')->group(function () {

            Route::get('/', [App\Http\Controllers\Admin\BorrowingController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\BorrowingController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\BorrowingController::class, 'store'])->name('store');
            Route::get('/{borrowing}', [App\Http\Controllers\Admin\BorrowingController::class, 'show'])->name('show');
            Route::get('/{borrowing}/edit', [App\Http\Controllers\Admin\BorrowingController::class, 'edit'])->name('edit');
            Route::put('/{borrowing}', [App\Http\Controllers\Admin\BorrowingController::class, 'update'])->name('update');
            Route::delete('/{borrowing}', [App\Http\Controllers\Admin\BorrowingController::class, 'destroy'])->name('destroy');

            Route::post('/{borrowing}/approve', [App\Http\Controllers\Admin\BorrowingController::class, 'approve'])->name('approve');
            Route::post('/{borrowing}/reject', [App\Http\Controllers\Admin\BorrowingController::class, 'reject'])->name('reject');
            Route::post('/{borrowing}/return', [App\Http\Controllers\Admin\BorrowingController::class, 'return'])->name('return');

        });

        /*
        | Transactions
        */

        Route::resource('transactions', App\Http\Controllers\Admin\TransactionController::class);

        Route::post('/transactions/{transaction}/approve', [App\Http\Controllers\Admin\TransactionController::class, 'approve'])->name('transactions.approve');
        Route::post('/transactions/{transaction}/reject', [App\Http\Controllers\Admin\TransactionController::class, 'reject'])->name('transactions.reject');

        Route::get('/transactions/user/{user}', [App\Http\Controllers\Admin\TransactionController::class, 'userTransactions'])->name('transactions.user-history');

        Route::get('/laporan', [App\Http\Controllers\Admin\DashboardController::class, 'laporan'])->name('laporan');

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


        /*
        | PEMINJAMAN
        */

Route::prefix('peminjaman')->name('peminjaman.')->group(function () {

    // halaman daftar peminjaman
    Route::get('/', [App\Http\Controllers\Petugas\PeminjamanController::class, 'index'])->name('index');

    // halaman form tambah peminjaman
    Route::get('/create', [App\Http\Controllers\Petugas\PeminjamanController::class, 'create'])->name('create');

    // proses simpan peminjaman dari form
    Route::post('/store', [App\Http\Controllers\Petugas\PeminjamanController::class, 'store'])->name('store');

    // update status peminjaman
    Route::put('/{id}/update-status', [App\Http\Controllers\Petugas\PeminjamanController::class, 'updateStatus'])->name('update-status');

    // hapus peminjaman
    Route::delete('/{id}', [App\Http\Controllers\Petugas\PeminjamanController::class, 'destroy'])->name('destroy');

    

});


/*
| PENGEMBALIAN
*/

Route::prefix('pengembalian')->name('pengembalian.')->group(function () {

    Route::get('/', [PengembalianController::class, 'index'])
        ->name('index');

    Route::get('/{id}/konfirmasi', [PengembalianController::class, 'konfirmasi'])
        ->name('konfirmasi');

    Route::post('/{id}/konfirmasi', [PengembalianController::class, 'konfirmasiStore'])
        ->name('konfirmasi.store');

    // HALAMAN PROSES
    Route::get('/{id}/proses', [PengembalianController::class, 'proses'])
        ->name('proses');

    // SIMPAN PROSES
    Route::post('/{id}/proses', [PengembalianController::class, 'prosesStore'])
        ->name('proses.store');
    
       

});



/*
| TRANSAKSI DENDA
*/


Route::prefix('transaksi')->name('transaksi.')->group(function () {

    Route::get('/', [TransaksiDendaController::class, 'index'])->name('index');

    Route::get('/create/{peminjaman_id}', [TransaksiDendaController::class, 'createTransaksi'])->name('create');

    Route::post('/store/{peminjaman_id}', [TransaksiDendaController::class, 'storeTransaksi'])->name('store');

    Route::get('/{id}', [TransaksiDendaController::class, 'show'])->name('show');

    Route::get('/{id}/pembayaran', [TransaksiDendaController::class, 'pembayaran'])->name('pembayaran');

    Route::post('/{id}/pembayaran', [TransaksiDendaController::class, 'prosesPembayaran'])->name('proses-pembayaran');

    Route::get('/{id}/struk', [TransaksiDendaController::class, 'cetakStruk'])->name('struk');

    // ROUTE EXPORT YANG BENAR
    Route::post('/export', [TransaksiDendaController::class,'export'])
        ->name('export');

});


        Route::get('/laporan', [App\Http\Controllers\Petugas\LaporanController::class, 'index'])->name('laporan');

    });


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

        /*
        | ALAT
        */

        Route::get('/alat', [App\Http\Controllers\User\AlatController::class, 'index'])->name('alat');


        /*
        | PEMINJAMAN
        */

        Route::prefix('peminjaman')->name('peminjaman.')->group(function () {

            Route::get('/', [App\Http\Controllers\User\PeminjamanController::class, 'index'])->name('index');

            Route::get('/create/{laptop_id?}', [App\Http\Controllers\User\PeminjamanController::class, 'create'])->name('create');

            Route::post('/', [App\Http\Controllers\User\PeminjamanController::class, 'store'])->name('store');

            Route::get('/{id}', [App\Http\Controllers\User\PeminjamanController::class, 'show'])->name('show');
            
                    Route::delete('/{id}', [App\Http\Controllers\User\PeminjamanController::class, 'destroy'])->name('destroy');

        });

            /*
    | PENGEMBALIAN - TAMBAHKAN INI
    */
    
    Route::prefix('pengembalian')->name('pengembalian.')->group(function () {
        
        // Halaman daftar peminjaman yang siap dikembalikan
        Route::get('/', [App\Http\Controllers\User\PengembalianController::class, 'index'])->name('index');
        
        // Halaman form pengembalian (opsional, jika perlu)
        Route::get('/create/{peminjaman_id?}', [App\Http\Controllers\User\PengembalianController::class, 'create'])->name('create');
        
        // Proses pengembalian
        Route::post('/{peminjaman}', [App\Http\Controllers\User\PengembalianController::class, 'store'])->name('store');
        
        // Detail pengembalian (jika diperlukan)
        Route::get('/{id}', [App\Http\Controllers\User\PengembalianController::class, 'show'])->name('show');
        
    });



        /*
        | RIWAYAT
        */

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