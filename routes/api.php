<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Trang home
Route::get('/home', [UserController::class, 'home']);


// Trang sản phẩm
Route::get('products/filters', [ProductController::class, 'filters']);
Route::get('products/search', [ProductController::class, 'search']);
Route::get('products', [ProductController::class, 'index']);

// Trang chi tiết sản phẩm
Route::get('products/{idOrSlug}', [ProductController::class, 'show']);

// Route đăng nhập
// Các route API được bảo vệ bằng `auth:sanctum`
Route::middleware('api')->group(function () {
    // Đăng nhập, đăng ký
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    // Đăng xuất cần xác thực
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
    // Các route quên mật khẩu
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [AuthController::class, 'reset']);
});

// Trang cart

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::put('/cart', [CartController::class, 'updateCart']);
    Route::delete('cart', [CartController::class, 'removeFromCart']);
});





