<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('profile/detail', [ProfileController::class, 'show']);
    Route::patch('profile/update', [ProfileController::class, 'update']);

    Route::get('users', [UserController::class, 'index']);
    Route::post('user/store', [UserController::class, 'store']);
    Route::get('users/{user}/detail', [UserController::class, 'show']);
    Route::patch('users/{user}/update', [UserController::class, 'update']);
    Route::delete('users/{user}/delete', [UserController::class, 'destroy']);

    // Warehouse Routes
    Route::post('warehouse/store', [WarehouseController::class, 'store']);
    Route::patch('warehouse/{id}/update', [WarehouseController::class, 'update']);
    Route::patch('warehouse/{id}/toggle-status', [WarehouseController::class, 'toggleStatus']);

    // Category Routes
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('category/store', [CategoryController::class, 'store']);
    Route::get('categories/{id}/detail', [CategoryController::class, 'show']);
    Route::patch('categories/{id}/update', [CategoryController::class, 'update']);
    Route::patch('categories/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
});
