<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

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