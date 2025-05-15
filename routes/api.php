<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Route::middleware('role:admin,agent,landlord')->group(function () {
        // Property routes
        Route::middleware('throttle:20,1')->apiResource('properties', \App\Http\Controllers\Properties\PropertyController::class);

        // Unit routes
        Route::apiResource('properties.units', \App\Http\Controllers\Properties\UnitController::class)
            ->shallow();

        // Lease routes
        Route::middleware('throttle:15,1')->apiResource('leases', \App\Http\Controllers\Leases\LeaseController::class);

        // Tenant routes
        Route::middleware('throttle:15,1')->apiResource('tenants', \App\Http\Controllers\Tenants\TenantController::class);

        // Payment routes
        Route::middleware('throttle:10,1')->apiResource('payments', \App\Http\Controllers\Payments\PaymentController::class);
    });
});
