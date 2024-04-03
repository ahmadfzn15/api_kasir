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
use App\Http\Controllers\StrukController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(UserController::class)->group(function() {
        Route::get('/user', 'index');
        Route::post('/user/update', 'update');
        Route::put('/user/change-password', 'changePassword');
        Route::delete('/user/{id}', 'destroy');
    });

    Route::controller(AuthController::class)->group(function() {
        Route::post('/user', 'store');
        Route::post('/logout', 'logout');
    });

    Route::controller(ProductController::class)->group(function() {
        Route::get('/product', 'index');
        Route::post('/product', 'store');
        Route::post('/product/{id}', 'update');
        Route::delete('/product/{id}', 'destroy');
    });

    Route::apiResource('/category', CategoryController::class);
    Route::apiResource('/cashier', EmployeeController::class);
    Route::apiResource('/variant', VariantController::class);
    Route::apiResource('/market', MarketController::class);

    Route::controller(MarketController::class)->group(function() {
        Route::delete('/market/{id}/reset', 'clear');
    });

    Route::controller(ProductController::class)->group(function() {
        Route::post('/product/delete', 'destroy');
    });

    Route::controller(SaleController::class)->group(function() {
        Route::get('/sale', 'index');
        Route::get('/sale/statistics', 'getSale');
        Route::get('/sale/{id}', 'show');
        Route::post('/sale', 'store');
        Route::put('/sale/{id}', 'update');
        Route::delete('/sale/{id}', 'destroy');
    });

    Route::controller(StrukController::class)->group(function() {
        Route::post('/struk', 'index');
    });

    Route::controller(AuthController::class)->group(function() {
        Route::post('/verify-email', 'verify');
        Route::post('/send-verify', 'sendVerify');
    });
});

Route::middleware(['guest'])->group(function () {
    Route::controller(AuthController::class)->group(function() {
        Route::post('/auth/login', 'login');
        Route::post('/auth/register', 'register');
    });
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    $response = [
        "success" => true,
        "message" => "Email verifikasi berhasil dikirim"
    ];

    return response()->json($response, 200);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::controller(PasswordResetLinkController::class)->group(function() {
    Route::post('/forgot-password', 'store');
});

