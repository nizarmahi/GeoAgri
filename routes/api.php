<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\TrenController;
use App\Http\Controllers\Api\PerProvinsiController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\ScrapingLogController;
use App\Http\Controllers\Api\NullAnalysisController;
use App\Http\Controllers\Api\PasarAnalysisController;

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

        Route::get('pasar-map',    [MapController::class, 'pasarMap']);
        // GET /api/komoditas/pasar-map?komoditas_id=1&tanggal=2024-01-15&provinsi_id=35

        Route::get('map',          MapController::class);
        // GET /api/komoditas/map?komoditas_id=1&tanggal=2024-01-15&level=provinsi
        // GET /api/komoditas/map?komoditas_id=1&tanggal=2024-01-15&level=kabupaten

        Route::get('heatmap',    [MapController::class, 'heatmapProxy']);
        // GET /api/komoditas/heatmap?komoditas=BUNCIS
        // Proxy ke API eksternal http://labai.polinema.ac.id:1901/api/heatmap

        // ── Null Analysis ──────────────────────────────────────────────
        Route::get('null-stats',        [NullAnalysisController::class, 'stats']);
        // GET /api/komoditas/null-stats

        Route::get('null-timeline',     [NullAnalysisController::class, 'timeline']);
        // GET /api/komoditas/null-timeline?days=7

        Route::get('null-by-pasar',     [NullAnalysisController::class, 'byPasar']);
        // GET /api/komoditas/null-by-pasar?per_page=5

        Route::get('null-batches',      [NullAnalysisController::class, 'batches']);
        // GET /api/komoditas/null-batches?limit=4

        Route::get('null-by-komoditas', [NullAnalysisController::class, 'byKomoditas']);
        // GET /api/komoditas/null-by-komoditas

        // ── Pasar Analysis ─────────────────────────────────────────────
        Route::get('analisis-pasar', PasarAnalysisController::class);
        // GET /api/komoditas/analisis-pasar?komoditas_id=&provinsi_id=&tanggal=&per_page=10
    });

    // ── Scraping Log ────────────────────────────────────────────
    Route::get('scraping/logs', [ScrapingLogController::class, 'index']);
    // GET /api/scraping/logs?status=&provinsi_id=&tanggal=&per_page=15

    Route::post('scraping/log', [ScrapingLogController::class, 'store']);
    // POST /api/scraping/log
});
