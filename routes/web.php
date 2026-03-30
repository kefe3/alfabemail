<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OgrenciController;
use App\Http\Controllers\VeliController;

// Ana sayfa — penguen animasyonu
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Öğrenci girişi (QR + form)
Route::get('/giris', function () {
    return view('ogrenci.giris');
})->name('ogrenci.giris');

Route::post('/giris/qr', [OgrenciController::class, 'qrLogin'])->name('ogrenci.qr-login');

// Veli dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/veli/dashboard', function () {
        return view('veli.dashboard');
    })->name('veli.dashboard');
});

// Aktivasyon linkleri
Route::get('/aktivasyon/{token}', [App\Http\Controllers\ActivationController::class, 'activate'])->name('activation.activate');
