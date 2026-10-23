<?php

use Illuminate\Support\Facades\Route;
use Modules\EIS\Http\Controllers\TerminalManagementController;

Route::prefix('eis')->group(function () {
    Route::get('/', 'EISController@index');
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
