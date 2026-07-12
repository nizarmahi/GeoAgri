<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminMasterController;
use App\Http\Controllers\Api\PipelineController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\TrenController;
use App\Http\Controllers\Api\PerProvinsiController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\ScrapingLogController;
use App\Http\Controllers\Api\NullAnalysisController;
use App\Http\Controllers\Api\PasarAnalysisController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes — Dashboard Komoditas Pertanian
|--------------------------------------------------------------------------
*/

// ── Auth (Public) ──────────────────────────────────────────────
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
});

// ── Public Read-Only (Guest bisa akses) ────────────────────────
Route::middleware('throttle:60,1')->group(function () {

    // ── Dashboard ───────────────────────────────────────────────
    Route::get('dashboard', DashboardController::class);

    // ── Master / Referensi ─────────────────────────────────────
    Route::prefix('master')->group(function () {
        Route::get('komoditas', [MasterController::class, 'komoditas']);
        Route::get('provinsi',  [MasterController::class, 'provinsi']);
        Route::get('kabkota',   [MasterController::class, 'kabkota']);
        Route::get('pasar',     [MasterController::class, 'pasar']);
    });

    // ── Data Komoditas ─────────────────────────────────────────
    Route::prefix('komoditas')->group(function () {
        Route::get('tren',         TrenController::class);
        Route::get('data',         [TrenController::class, 'data']);
        Route::get('per-provinsi', PerProvinsiController::class);

        Route::get('pasar-map',    [MapController::class, 'pasarMap']);
        Route::get('map',          MapController::class);
        Route::get('heatmap',    [MapController::class, 'heatmapProxy']);

        // ── Null Analysis ──────────────────────────────────────
        Route::get('null-stats',        [NullAnalysisController::class, 'stats']);
        Route::get('null-timeline',     [NullAnalysisController::class, 'timeline']);
        Route::get('null-by-pasar',     [NullAnalysisController::class, 'byPasar']);
        Route::get('null-batches',      [NullAnalysisController::class, 'batches']);
        Route::get('null-by-komoditas', [NullAnalysisController::class, 'byKomoditas']);

        // ── Pasar Analysis ─────────────────────────────────────
        Route::get('analisis-pasar', PasarAnalysisController::class);
    });

    // ── Scraping Log (Read Only) ───────────────────────────────
    Route::get('scraping/logs', [ScrapingLogController::class, 'index']);
});

// ── Admin Only (Login + Role admin) ────────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // ── Pipeline ───────────────────────────────────────────────
    Route::get('pipeline/status', [PipelineController::class, 'status']);
    Route::post('pipeline/trigger', [PipelineController::class, 'triggerScraping']);

    // ── Scraping Log (Write) ───────────────────────────────────
    Route::post('scraping/logs', [ScrapingLogController::class, 'store']);

    // ── Master Data CRUD ───────────────────────────────────────
    Route::get('provinsi', [AdminMasterController::class, 'indexProvinsi']);
    Route::post('provinsi', [AdminMasterController::class, 'storeProvinsi']);
    Route::put('provinsi/{id}', [AdminMasterController::class, 'updateProvinsi']);
    Route::delete('provinsi/{id}', [AdminMasterController::class, 'destroyProvinsi']);

    Route::get('kabkota', [AdminMasterController::class, 'indexKabkota']);
    Route::post('kabkota', [AdminMasterController::class, 'storeKabkota']);
    Route::put('kabkota/{id}', [AdminMasterController::class, 'updateKabkota']);
    Route::delete('kabkota/{id}', [AdminMasterController::class, 'destroyKabkota']);

    Route::get('pasar', [AdminMasterController::class, 'indexPasar']);
    Route::post('pasar', [AdminMasterController::class, 'storePasar']);
    Route::put('pasar/{id}', [AdminMasterController::class, 'updatePasar']);
    Route::delete('pasar/{id}', [AdminMasterController::class, 'destroyPasar']);

    Route::get('komoditas', [AdminMasterController::class, 'indexKomoditas']);
    Route::post('komoditas', [AdminMasterController::class, 'storeKomoditas']);
    Route::put('komoditas/{id}', [AdminMasterController::class, 'updateKomoditas']);
    Route::delete('komoditas/{id}', [AdminMasterController::class, 'destroyKomoditas']);

    Route::get('kategori', [AdminMasterController::class, 'indexKategori']);
    Route::post('kategori', [AdminMasterController::class, 'storeKategori']);
    Route::put('kategori/{id}', [AdminMasterController::class, 'updateKategori']);
    Route::delete('kategori/{id}', [AdminMasterController::class, 'destroyKategori']);
});
