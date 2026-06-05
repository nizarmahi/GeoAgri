<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\TrenController;
use App\Http\Controllers\Api\PerProvinsiController;
use App\Http\Controllers\Api\MapController;

/*
|--------------------------------------------------------------------------
| API Routes — Dashboard Komoditas Pertanian
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:60,1')->group(function () {

    // ── Master / Referensi ─────────────────────────────────────────────
    Route::prefix('master')->group(function () {
        Route::get('komoditas', [MasterController::class, 'komoditas']);
        // GET /api/master/komoditas

        Route::get('provinsi',  [MasterController::class, 'provinsi']);
        // GET /api/master/provinsi

        Route::get('pasar',     [MasterController::class, 'pasar']);
        // GET /api/master/pasar
    });

    // ── Data Komoditas ─────────────────────────────────────────────────
    Route::prefix('komoditas')->group(function () {
        Route::get('tren',         TrenController::class);
        // GET /api/komoditas/tren?komoditas_id=1&from=2024-01-01&to=2024-01-31
        // GET /api/komoditas/tren?komoditas_id=1&provinsi_id=35&from=...

        Route::get('data',         [TrenController::class, 'data']);
        // GET /api/komoditas/data?komoditas_id=1&from=2024-01-01&to=2024-01-31&limit=500

        Route::get('per-provinsi', PerProvinsiController::class);
        // GET /api/komoditas/per-provinsi?komoditas_id=1&tanggal=2024-01-15

        Route::get('map',          MapController::class);
        // GET /api/komoditas/map?komoditas_id=1&tanggal=2024-01-15&level=provinsi
        // GET /api/komoditas/map?komoditas_id=1&tanggal=2024-01-15&level=kabupaten
    });

});
