<?php

use App\Http\Controllers\Authentication;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
});
Route::get('/login', [Authentication::class, 'login'])->middleware('guest')->name('login');
Route::post('/login', [Authentication::class, 'authenticate'])->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [Authentication::class, 'logout'])->name('logout');

    Route::get('/dashboard', [dashboard::class, 'index'])->name('dashboard');

    // BARANG
    Route::prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index']);
        Route::get('/create', [BarangController::class, 'create']);
        Route::post('/', [BarangController::class, 'store']);
        Route::get('/{id}/edit', [BarangController::class, 'edit']);
        Route::put('/{id}', [BarangController::class, 'update']);
        Route::delete('/{id}', [BarangController::class, 'destroy']);
    });

    // SUPPLIER
    Route::prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::get('/create', [SupplierController::class, 'create']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/{id}/edit', [SupplierController::class, 'edit']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
    });

    // PEMBELIAN
    Route::resource('pembelian', PembelianController::class);

    // GUDANG
    Route::resource('gudang', GudangController::class);
    Route::get('/gudang/{gudangId}/barang', [GudangController::class, 'getBarangByGudang'])->name('gudang.barang');

    // PELANGGAN
    Route::resource('pelanggan', PelangganController::class);

    // PENJUALAN
    Route::resource('penjualan', PenjualanController::class);

    // COA (Chart of Accounts) - Read Only
    Route::get('coa', [CoaController::class, 'index'])->name('coa.index');

    // JURNAL UMUM - Read Only (Auto Generated)
    Route::get('jurnal-umum', [JurnalUmumController::class, 'index'])->name('jurnal-umum.index');

    // BUKU BESAR - Read Only (From Jurnal Umum)
    Route::get('buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');
});
