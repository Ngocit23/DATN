<?php

use Illuminate\Support\Facades\Route;
// routes/web.php
use App\Http\Controllers\Api\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Route quên mật khẩu
Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Route login phải có trong web.php
Route::post('login', [AuthController::class, 'login'])->name('login');

// Các route khác liên quan đến reset password
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');