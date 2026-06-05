<?php

use App\Http\Controllers\Api\KomoditasController;
use App\Http\Controllers\Api\MasterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Dashboard Harga Komoditas
|--------------------------------------------------------------------------
|
| Semua route menggunakan prefix /api (dari bootstrap/app.php atau
| RouteServiceProvider tergantung versi Laravel Anda).
|
| Contoh akses: GET https://domain.com/api/komoditas/tren?komoditas=Bawang+Merah
|
*/

Route::prefix('komoditas')->group(function () {

    // Tren harga harian (untuk grafik garis)
    // GET /api/komoditas/tren?komoditas=Bawang Merah&dari=2026-01-01&sampai=2026-01-31&provinsi_id=32
    Route::get('/tren', [KomoditasController::class, 'tren']);

    // Rata-rata harga per provinsi (untuk tabel & choropleth)
    // GET /api/komoditas/per-provinsi?komoditas=Bawang Merah&dari=2026-01-01&sampai=2026-01-31
    Route::get('/per-provinsi', [KomoditasController::class, 'perProvinsi']);

    // GeoJSON untuk Leaflet map
    // GET /api/komoditas/map?komoditas=Bawang Merah&dari=2026-01-01&sampai=2026-01-31
    Route::get('/map', [KomoditasController::class, 'map']);
});

Route::prefix('master')->group(function () {

    // List komoditas unik (dropdown filter)
    // GET /api/master/komoditas
    Route::get('/komoditas', [MasterController::class, 'komoditas']);

    // List provinsi
    // GET /api/master/provinsi
    Route::get('/provinsi', [MasterController::class, 'provinsi']);

    // List pasar, filter opsional
    // GET /api/master/pasar?provinsi_id=32&kabkota_id=3204
    Route::get('/pasar', [MasterController::class, 'pasar']);
});
