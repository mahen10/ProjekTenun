<?php

use App\Http\Controllers\ApiController\produk_tenunController;
use App\Http\Controllers\ApiController\TenunController;
use App\Http\Controllers\ApiController\pembelian_bahanController;
use App\Http\Controllers\ApiController\penjualanController;
use App\Http\Controllers\ApiController\laporan_bulananController;
use App\Http\Controllers\ApiController\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Whoops\Run;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(
    function () {
        // 📦 PRODUK TENUN (stok barang)
        Route::get('/produk', [produk_tenunController::class, 'index']);        // List semua produk vendor login
        Route::post('/produk', [produk_tenunController::class, 'store']);       // Tambah produk
        Route::get('/produk/{id}', [produk_tenunController::class, 'show']);    // Detail produk
        Route::put('/produk/{id}', [produk_tenunController::class, 'update']);  // Update produk
        Route::delete('/produk/{id}', [produk_tenunController::class, 'destroy']); // Hapus produk

        // 🧵 TENUN (vendor)
        Route::get('/tenun', [TenunController::class, 'index']);        // List semua vendor (super admin)
        Route::post('/tenun', [TenunController::class, 'store']);       // Tambah vendor
        Route::get('/tenun/{id}', [TenunController::class, 'show']);    // Detail vendor
        Route::put('/tenun/{id}', [TenunController::class, 'update']);  // Update vendor
        Route::delete('/tenun/{id}', [TenunController::class, 'destroy']); // Hapus vendor
        


        // 📦 PENJUALAN
        Route::get('/penjualan', [penjualanController::class, 'index']);        // Semua penjualan
        Route::post('/penjualan', [penjualanController::class, 'store']);       // Tambah penjualan
        Route::get('/penjualan/{id}', [penjualanController::class, 'show']);    // Detail penjualan
        Route::put('/penjualan/{id}', [penjualanController::class, 'update']);  // Update penjualan
        Route::delete('/penjualan/{id}', [penjualanController::class, 'destroy']); // Hapus penjualan

        // 💸 PEMBELIAN BAHAN (pendapatan versi bahan baku masuk)
        Route::get('/pembelian-bahan', [pembelian_bahanController::class, 'index']);
        Route::post('/pembelian-bahan', [pembelian_bahanController::class, 'store']);
        Route::get('/pembelian-bahan/{id}', [pembelian_bahanController::class, 'show']);
        Route::put('/pembelian-bahan/{id}', [pembelian_bahanController::class, 'update']);
        Route::delete('/pembelian-bahan/{id}', [pembelian_bahanController::class, 'destroy']);

        // 📊 LAPORAN BULANAN
        Route::get('/laporan-bulanan', [laporan_bulananController::class, 'index']);
        Route::post('/laporan-bulanan', [laporan_bulananController::class, 'store']);
        Route::get('/laporan-bulanan/{id}', [laporan_bulananController::class, 'show']);
        Route::put('/laporan-bulanan/{id}', [laporan_bulananController::class, 'update']);
        Route::delete('/laporan-bulanan/{id}', [laporan_bulananController::class, 'destroy']);
    }




);
