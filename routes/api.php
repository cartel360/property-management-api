<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Route::middleware('role:admin,agent,landlord')->group(function () {
        // Property routes
        Route::apiResource('properties', \App\Http\Controllers\Properties\PropertyController::class);

        // // Unit routes
        // Route::apiResource('properties.units', \App\Http\Controllers\Properties\UnitController::class)
        //     ->shallow();

        // // Tenant routes
        // Route::apiResource('tenants', \App\Http\Controllers\Tenants\TenantController::class);

        // // Lease routes
        // Route::apiResource('leases', \App\Http\Controllers\Leases\LeaseController::class);

        // // Payment routes
        // Route::apiResource('payments', \App\Http\Controllers\Payments\PaymentController::class);
        // });
    });
});
