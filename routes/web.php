<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SendWhatsappController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/laporan', [ReportController::class, 'index'])->name('admin.laporan');
    Route::get('/send-whatsapp/{record}', [SendWhatsappController::class, 'send'])->name('sendWa');
    // Route::get('/laporan/penjualan', [ReportController::class, 'penjualan'])->name('admin.laporan.penjualan');
    // Route::get('/laporan/stok', [ReportController::class, 'stok'])->name('admin.laporan.stok');
});

require __DIR__ . '/auth.php';
