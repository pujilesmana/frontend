<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// user route
// Route::get('/user', function () {
//     return 'Selamat datang! Token Backend: ' . session('tokenBackend');
// })->name('user');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/data', [UserController::class, 'fetchUsers'])->name('users.data');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::post('/qrcode', [UserController::class, 'generateQrCode'])->name('users.qrcode');
Route::post('/barcode', [UserController::class, 'generateBarcode'])->name('users.barcode');