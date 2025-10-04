<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CategoryController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Products CRUD
    Route::apiResource('products', ProductController::class);

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Receipt PDF
    Route::get('orders/{order}/receipt', [ReceiptController::class, 'show']);
    Route::get('orders/{order}/receipt/pdf', [ReceiptController::class, 'pdf']);
});