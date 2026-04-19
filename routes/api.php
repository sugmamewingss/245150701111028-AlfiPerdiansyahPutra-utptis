<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Route API untuk mengelola data barang (ecommerce-like backend).
| Semua route memiliki prefix /api secara otomatis dari Laravel.
|
*/

// GET /api/barang — Menampilkan semua barang
Route::get('/barang', [BarangController::class, 'index']);

// GET /api/barang/{id} — Menampilkan barang berdasarkan ID
Route::get('/barang/{id}', [BarangController::class, 'show']);

// POST /api/barang — Membuat barang baru
Route::post('/barang', [BarangController::class, 'store']);

// PUT /api/barang/{id} — Mengedit seluruh data barang
Route::put('/barang/{id}', [BarangController::class, 'update']);

// PATCH /api/barang/{id} — Mengedit sebagian data barang
Route::patch('/barang/{id}', [BarangController::class, 'partialUpdate']);

// DELETE /api/barang/{id} — Menghapus barang
Route::delete('/barang/{id}', [BarangController::class, 'destroy']);
