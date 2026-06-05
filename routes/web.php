<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes — Dashboard Komoditas
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    Route::get('/dashboard',      [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/analisis-harga', [DashboardController::class, 'analisisHarga'])
        ->name('analisis-harga');

    Route::get('/analisis-null',  [DashboardController::class, 'analisisNull'])
        ->name('analisis-null');

    Route::get('/analisis-pasar',  [DashboardController::class, 'analisisPasar'])
        ->name('analisis-pasar');

    Route::get('/data',           [DashboardController::class, 'data'])
        ->name('data.index');
});
