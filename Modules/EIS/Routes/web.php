<?php

use Illuminate\Support\Facades\Route;
use Modules\EIS\Http\Controllers\StockOutboundController;
use Modules\EIS\Http\Controllers\TerminalManagementController;
use Modules\EIS\Http\Controllers\UtilitiesController;

Route::prefix('eis/utilities')->middleware('auth')->name('eis.utilities.')->group(function () {
    Route::get('/', [UtilitiesController::class, 'index'])->name('index');
    Route::post('execute', [UtilitiesController::class, 'execute'])->name('execute');
    Route::get('requests/{requestId}', [UtilitiesController::class, 'show'])->whereNumber('requestId')->name('show');
    Route::post('requests/{requestId}/submit', [UtilitiesController::class, 'submit'])->whereNumber('requestId')->name('submit');
});

Route::prefix('eis')->group(function () {
    Route::get('/', 'EISController@index');
});

Route::prefix('eis/stock')->middleware('auth')->name('eis.stock.')->group(function () {
    Route::get('/', [\Modules\EIS\Http\Controllers\StockReconciliationController::class, 'index'])->name('index');
    Route::post('preview', [\Modules\EIS\Http\Controllers\StockReconciliationController::class, 'preview'])->name('preview');
    Route::post('{snapshotId}/apply', [\Modules\EIS\Http\Controllers\StockReconciliationController::class, 'apply'])
        ->whereNumber('snapshotId')->name('apply');
});

// Session authentication and the web middleware's CSRF protection are required.
Route::prefix('eis/terminals')->middleware(['web', 'auth'])->name('eis.terminals.')->group(function () {
    Route::get('/', [TerminalManagementController::class, 'index'])->name('index');
    Route::post('activate', [TerminalManagementController::class, 'activate'])->name('activate');
    Route::post('{terminalId}/latest-config', [TerminalManagementController::class, 'latestConfig'])
        ->whereNumber('terminalId')->name('latest-config');
    Route::post('{terminalId}/deactivate', [TerminalManagementController::class, 'deactivate'])
        ->whereNumber('terminalId')->name('deactivate');
    Route::post('{terminalId}/renew-tokens', [TerminalManagementController::class, 'renewTokens'])
        ->whereNumber('terminalId')->name('renew-tokens');
    Route::post('{terminalId}/default', [TerminalManagementController::class, 'setDefault'])
        ->whereNumber('terminalId')->name('default');
});

Route::prefix('eis/stock/outbound')->middleware(['web', 'auth'])->name('eis.stock.outbound.')->group(function () {
    Route::get('/', [StockOutboundController::class, 'index'])->name('index');
    Route::post('/', [StockOutboundController::class, 'store'])->name('store');
    Route::post('{operationId}/dispatch', [StockOutboundController::class, 'dispatchQueued'])
        ->whereNumber('operationId')->name('dispatch');
    Route::post('{operationId}/verify', [StockOutboundController::class, 'verify'])
        ->whereNumber('operationId')->name('verify');
    Route::get('locations/{locationId}/adjustment-reasons', [StockOutboundController::class, 'reasons'])
        ->whereNumber('locationId')->name('reasons');
});
