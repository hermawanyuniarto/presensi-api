<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PresensiController;

// Ini akan membuat URL: http://localhost/presensi-api/public/api/login
// Menerima kiriman data (POST) dari aplikasi Android
Route::post('/login', [AuthController::class, 'login']);

// JALUR API BARU UNTUK MENYIMPAN DATA
Route::post('/presensi', [PresensiController::class, 'store']);
Route::post('/ijin', [PresensiController::class, 'storeIjin']);

// JALUR API UNTUK MENGAMBIL RIWAYAT (BARU)
Route::get('/presensi/riwayat/{nim}', [PresensiController::class, 'riwayat']);

// JALUR API BARU: MENGAMBIL JADWAL SHIFT DINAMIS DARI DATABASE
Route::get('/shifts/{kelompok}', [PresensiController::class, 'getShifts']);

// JALUR API BARU: MENGAMBIL KONFIGURASI GPS UNTUK HP
Route::get('/konfigurasi', [PresensiController::class, 'getKonfigurasi']);

Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Presensi RSSA Berjalan Normal!'
    ]);
});
