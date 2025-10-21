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
Route::get('/products', [UserController::class, 'getProducts']);
Route::get('/products/{slug}', [UserController::class, 'getProductDetail']);
Route::get('/products/search', [UserController::class, 'searchProducts']);


// Trang sản phẩm
Route::get('products/filters', [ProductController::class, 'filters']);
Route::get('products', [ProductController::class, 'index']);