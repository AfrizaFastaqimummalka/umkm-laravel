<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\InventoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TelegramController;

// Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Telegram webhook (no auth needed)
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');

Route::get('/', fn() => redirect()->route('dashboard'));

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pemasukan
    Route::get('/pemasukan',                [PemasukanController::class,   'index'])->name('pemasukan.index');
    Route::post('/pemasukan',               [PemasukanController::class,   'store'])->name('pemasukan.store');
    Route::put('/pemasukan/{pemasukan}',    [PemasukanController::class,   'update'])->name('pemasukan.update');
    Route::delete('/pemasukan/{pemasukan}', [PemasukanController::class,   'destroy'])->name('pemasukan.destroy');

    // Pengeluaran
    Route::get('/pengeluaran',                  [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran',                 [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::put('/pengeluaran/{pengeluaran}',    [PengeluaranController::class, 'update'])->name('pengeluaran.update');
    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

    // Pelanggan
    Route::get('/pelanggan',                [PelangganController::class,   'index'])->name('pelanggan.index');
    Route::post('/pelanggan',               [PelangganController::class,   'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{pelanggan}',    [PelangganController::class,   'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{pelanggan}', [PelangganController::class,   'destroy'])->name('pelanggan.destroy');

    // Pemasok
    Route::get('/pemasok',                  [PemasokController::class,    'index'])->name('pemasok.index');
    Route::post('/pemasok',                 [PemasokController::class,    'store'])->name('pemasok.store');
    Route::put('/pemasok/{pemasok}',        [PemasokController::class,    'update'])->name('pemasok.update');
    Route::delete('/pemasok/{pemasok}',     [PemasokController::class,    'destroy'])->name('pemasok.destroy');

    // Inventori
    Route::get('/inventori',                        [InventoriController::class,   'index'])->name('inventori.index');
    Route::post('/inventori',                       [InventoriController::class,   'store'])->name('inventori.store');
    Route::put('/inventori/{inventori}',            [InventoriController::class,   'update'])->name('inventori.update');
    Route::delete('/inventori/{inventori}',         [InventoriController::class,   'destroy'])->name('inventori.destroy');
    Route::post('/inventori/{inventori}/adjust',    [InventoriController::class,   'adjust'])->name('inventori.adjust');

    // Laporan
    Route::get('/laporan',          [LaporanController::class,  'index'])->name('laporan.index');
    Route::get('/laporan/generate', [LaporanController::class,  'generate'])->name('laporan.generate');
    Route::get('/laporan/export',   [LaporanController::class,  'export'])->name('laporan.export');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/sub/{subscriber}/toggle', [SettingsController::class, 'toggleSubscriber'])->name('settings.subscriber.toggle');
});
