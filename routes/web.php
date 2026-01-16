<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ReturnController;


Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    return view('index');
})->name('home');


Route::middleware(['guest', 'prevent-back-history'])->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin', 'prevent-back-history'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard');    

    // Produk
    Route::get('/products', [BarangController::class, 'index'])->name('admin.products');
    Route::post('/products', [BarangController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{barang}', [BarangController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{barang}', [BarangController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/import', [BarangController::class, 'import'])
    ->name('admin.products.import');
 
    //stok
    Route::get('/stock', [BarangController::class, 'stock'])->name('admin.stock');
    Route::post('/stock', [BarangController::class, 'updateStock'])->name('admin.stock.update');
    
    //Laporan
    Route::get('/reports', [LaporanController::class, 'index'])
    ->name('admin.reports');
    Route::get('/reports/excel', [LaporanController::class, 'exportExcel'])
        ->name('admin.reports.excel');
    Route::get('/reports/pdf', [LaporanController::class, 'exportPdf'])
        ->name('admin.reports.pdf');
        
    // users
    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');

    // Stock Movements
    Route::get('/admin/stock-movement', [BarangController::class, 'stockMovement'])
    ->middleware(['auth', 'role:admin'])->name('admin.stock-movement.index');

    // Price History
     Route::get('/price-history', [PriceController::class, 'index'])->name('admin.price-history.index');

    //Laporan Keuangan Laba dan Rugi
    Route::get('/admin/accounting/profit-loss', [LaporanController::class, 'profitLoss'])
    ->middleware(['auth', 'role:admin'])->name('admin.accounting.profit-loss');


});

Route::middleware(['auth', 'role:user', 'prevent-back-history'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
            ->name('user.dashboard');
    Route::get('/transaksi', fn () => view('user.transaksi'))->name('user.transaksi');
    Route::get('/history', fn () => view('user.history'))->name('user.history');

    // menu return
  Route::get('/return', [ReturnController::class, 'index'])
        ->name('user.return.index');

    Route::get('/return/create', [ReturnController::class, 'create'])
        ->name('user.return.create');

    Route::post('/return', [ReturnController::class, 'store'])
        ->name('user.return.store');

    Route::get('/return/{id}', [ReturnController::class, 'show'])
        ->name('user.return.show');
});



