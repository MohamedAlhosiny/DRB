<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductController2;
use App\Http\Controllers\Api\UserController;
use App\Models\Admin;
use App\Models\Product;
use Phiki\Phast\Root;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//For Users
Route::prefix('/user')->group(function () {
    Route::post('/register', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);

    Route::middleware('auth:sanctum', 'role.user')->group(function () {

        Route::get('/logout', [UserController::class, 'logout']);
    });
});

//For Admins

Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/login', [AdminController::class, 'login']);

    Route::middleware(['auth:sanctum', 'role.admin'])->group(function () {
        Route::get('/logout', [AdminController::class, 'logout']);
    });
});


// Admin privileges
Route::middleware('auth:sanctum', 'role.admin')->group(function () {
    //Categories
    Route::prefix('/categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::post('/{id}', [CategoryController::class, 'update']);
    });

    //Products
    Route::prefix('/products')->group(function () {
        Route::get('/', [ProductController2::class, 'index']);
        Route::get('/{id}', [ProductController2::class, 'show']);
        Route::post('/', [ProductController2::class, 'store']);
        Route::post('/{id}/status', [ProductController2::class, 'changeStatus']);
        Route::post('/{id}', [ProductController2::class, 'update']);
        Route::delete('/{id}', [ProductController2::class, 'destroy']);
    });
});

Route::prefix('/order')->group(function () {
    // User privileges on order
    Route::middleware('auth:sanctum', 'role.user')->group(function () {
        Route::post('/', [OrderController::class, 'store']);

    });

    //Admin privileges on order
    Route::middleware('auth:sanctum' , 'role.admin')->group(function() {
        Route::get('/' , [OrderController::class , 'index']);
    });
});



// Route::get('/product' , [ProductController::class , "index"])->name('product.index');

Route::post('/product', [ProductController::class, 'store']);
Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::post('/product/{id}', [ProductController::class, 'update']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);
