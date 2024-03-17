<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\Auth\AuthController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/user', AuthController::class);

    Route::controller(AuthController::class)->group(function() {
        Route::put('/user/update', 'update');
        Route::post('/logout', 'logout');
        Route::put('/user/change-password/{id}', 'changePassword');
    });

    Route::apiResource('/product', ProductController::class);
    Route::apiResource('/category', CategoryController::class);
    Route::apiResource('/cashier', CashierController::class);
    Route::apiResource('/sale', SaleController::class);
    Route::apiResource('/variant', VariantController::class);
    Route::apiResource('/market', MarketController::class);

    Route::controller(ProductController::class)->group(function() {
        Route::post('/product/delete', 'destroy');
    });

    Route::controller(SaleController::class)->group(function() {
    });
});

Route::controller(AuthController::class)->group(function() {
    Route::post('/auth/login', 'login');
    Route::post('/auth/register', 'register');
});

