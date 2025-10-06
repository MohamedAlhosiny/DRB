<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\ProductController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Models\Product;
use Phiki\Phast\Root;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/user')->group(function () {
    Route::post('/register' , [UserController::class , 'store']);
    Route::post('/login' , [UserController::class , 'login']);

    Route::middleware('auth:sanctum')->group(function () {

    Route::get('/logout' , [UserController::class , 'logout']);
});



});




// Route::get('/product' , [ProductController::class , "index"])->name('product.index');

Route::post('/product' , [ProductController::class , 'store']);
Route::get('/product' , [ProductController::class , 'index']);
Route::get('/product/{id}' , [ProductController::class , 'show']);
Route::post('/product/{id}' , [ProductController::class , 'update']);
Route::delete('/product/{id}' , [ProductController::class , 'destroy']);
