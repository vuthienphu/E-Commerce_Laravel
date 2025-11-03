<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/products', [ProductController::class, 'index']);
// Route::get('/products/{id}', [ProductController::class, 'show']);
// Route::get('/products/category/{categoryId}', [CategoryController::class, 'getProducts']);
// Route::get('/search',[ProductController::class, 'search']);
//  Route::get('/category', [CategoryController::class, 'index']);

// // Auth routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // Routes yêu cầu đăng nhập (user, admin, guest đều có token)
// Route::middleware(['auth:api'])->group(function () {

//     // ✅ admin và user mới được thêm/sửa sản phẩm
//     Route::middleware(['role:admin'])->group(function () {
//         Route::post('/products', [ProductController::class, 'store']);
//         Route::post('/products/{id}', [ProductController::class, 'update']);
//         Route::post('/category', [CategoryController::class, 'store']);
//         Route::post('/category/{id}', [CategoryController::class, 'update']);
//           Route::delete('/products/{id}', [ProductController::class, 'destroy']);
//         Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
//     });


// });



Route::get('/products', [ProductController::class, 'index']);
 Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/category/{categoryId}', [CategoryController::class, 'getProducts']);
 Route::get('/search',[ProductController::class, 'search']);
  Route::get('/category', [CategoryController::class, 'index']);
  Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
 Route::post('/products', [ProductController::class, 'store']);
       Route::post('/products/{id}', [ProductController::class, 'update']);
       Route::post('/category', [CategoryController::class, 'store']);
    Route::post('/category/{id}', [CategoryController::class, 'update']);
       Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
Route::post('/refreshToken', [AuthController::class, 'refreshToken']);
