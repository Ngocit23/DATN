<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/', function () {
    return view('welcome');  // Đây là trang web mặc định của bạn
});

// Route quên mật khẩu
Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Chỉ định các route liên quan đến reset mật khẩu
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');
