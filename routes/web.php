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
use App\Http\Controllers\Admin\LaporanReturnController;
use App\Http\Controllers\Admin\AccountingController;


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
    
    //Laporan stok transaksi
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
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])
        ->name('admin.users.activate');

    // Price History (ADMIN)
    Route::get('/price-history', [PriceController::class, 'history'])
    ->name('admin.price-history.index');

    // Update harga (EVENT)
     Route::put('/products/{barang}/price', [PriceController::class, 'update'])
    ->name('admin.products.price.update');

    //Laporan Return barang & stok
    Route::get('/admin/laporan-return', [LaporanReturnController::class, 'index'])
    ->name('admin.laporan-return');

    Route::get('/admin/laporan-return/excel', [LaporanReturnController::class, 'exportExcel'])
    ->name('admin.laporan-return.excel');

    Route::get('/admin/laporan-return/pdf', [LaporanReturnController::class, 'exportPdf'])
    ->name('admin.laporan-return.pdf');

    //Laporan Keuangan Laba dan Rugi
    Route::get('/accounting', [AccountingController::class, 'index'])
        ->name('admin.accounting.index');

    Route::get('/accounting/export/pdf', [AccountingController::class, 'exportPdf'])
        ->name('admin.accounting.export.pdf');

    Route::get('/accounting/export/excel', [AccountingController::class, 'exportExcel'])
        ->name('admin.accounting.export.excel');

    Route::get('/laporan-penjualan/{kode}', [AccountingController::class, 'show'])
        ->name('admin.laporan-penjualan.show');

});

Route::middleware(['auth', 'active', 'role:user', 'prevent-back-history'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
            ->name('user.dashboard');
    Route::get('/transaksi', fn () => view('user.transaksi'))->name('user.transaksi');
    Route::get('/history', fn () => view('user.history'))->name('user.history');

    // menu return
  Route::get('/return', [ReturnController::class, 'index'])
        ->name('user.return.index');

    Route::get('/return/create', [ReturnController::class, 'create'])
        ->name('user.return.create');

    Route::get('/return/items/{id}', [ReturnController::class, 'getItems'])
        ->name('user.return.items');

    Route::post('/return', [ReturnController::class, 'store'])
        ->name('user.return.store');

    Route::get('/return/{id}', [ReturnController::class, 'show'])
        ->name('user.return.show');
});
