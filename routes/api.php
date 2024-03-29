<?php

use App\Http\Controllers\SupplierApiController;
use App\Http\Controllers\EmployeeApiController;

use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\CustomerApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

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

//  localhost/tokolaravel/public/api/login
//api/logout
//api/refresh
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Middleware auth:sanctum digunakan untuk memastikan hanya user yang telah login yang dapat mengakses route di bawah ini /logout /refres
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Route untuk operasi CRUD produk dengan middleware auth:sanctum  (sama seperti diatas)
Route::prefix('products')->middleware('auth:sanctum')->group(function () {
    Route::get('', [ProductApiController::class, 'index']);
    Route::post('', [ProductApiController::class, 'store']);
    Route::get('{id}', [ProductApiController::class, 'show']);
    Route::put('{id}', [ProductApiController::class, 'update']);
    Route::delete('{id}', [ProductApiController::class, 'destroy']);
});
