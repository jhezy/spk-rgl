<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\ProduksiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/forecast', [ForecastController::class, 'index'])->name('forecast.index');
    Route::post('/forecast/run', [ForecastController::class, 'run'])->name('forecast.run');

    Route::resource('penjualan', PenjualanController::class);
    Route::resource('permintaan', PermintaanController::class);
    Route::resource('produksi', ProduksiController::class);

    Route::get('/forecast/export/pdf/{id}', [ForecastController::class, 'exportPDF'])->name('forecast.export.pdf');
});
