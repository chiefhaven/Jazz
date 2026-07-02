<?php

use Illuminate\Support\Facades\Route;
use App\EIS\Controllers\OnboardingController;
use App\EIS\Controllers\UtilityController;
use App\EIS\Controllers\ConfigurationController;
use App\EIS\Controllers\SalesController;

Route::prefix('eis/onboarding')->group(function () {
    Route::post('/activate', [OnboardingController::class, 'activate']);
    Route::post('/confirm', [OnboardingController::class, 'confirm']);
    Route::get('/status', [OnboardingController::class, 'status']);
});
Route::middleware(['eis.auth'])->prefix('eis')->group(function () {

    Route::prefix('eis/utilities')->group(function () {
        Route::get('/ping', [UtilityController::class, 'ping']);
        Route::post('/validate-vat', [UtilityController::class, 'validateVat']);
        Route::post('/validate-auth', [UtilityController::class, 'validateAuth']);
        Route::post('/check-tin', [UtilityController::class, 'checkTin']);
        Route::get('/terminal-blocking', [UtilityController::class, 'terminalBlocking']);
        Route::get('/unblock-status', [UtilityController::class, 'unblockStatus']);
        Route::get('/products', [UtilityController::class, 'products']);
        Route::post('/product-status', [UtilityController::class, 'productStatus']);
        Route::post('/upload-inventory', [UtilityController::class, 'uploadInventory']);
    });

    Route::prefix('eis/configuration')->group(function () {
        Route::get('/latest', [ConfigurationController::class, 'latest']);
        Route::post('/request-token', [ConfigurationController::class, 'requestToken']);
    });

    Route::prefix('eis/sales')->group(function () {
        Route::post('/submit', [SalesController::class, 'submit']);
        Route::post('/get', [SalesController::class, 'show']);
        Route::post('/credit-debit', [SalesController::class, 'creditDebit']);
        Route::post('/cancel', [SalesController::class, 'cancel']);
        Route::get('/cancelled', [SalesController::class, 'cancelled']);
        Route::get('/last-online', [SalesController::class, 'lastOnline']);
        Route::get('/last-offline', [SalesController::class, 'lastOffline']);
    });


    Route::prefix('sales')->group(function () {
        Route::post('/submit', [SalesController::class, 'submit']);
        Route::post('/get', [SalesController::class, 'show']);
    });
});