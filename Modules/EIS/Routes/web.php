<?php

use Illuminate\Support\Facades\Route;
use Modules\EIS\Http\Controllers\TerminalActivationController as WebTerminalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('eis')->group(function() {
    Route::get('/', 'EISController@index');
});


/*
|--------------------------------------------------------------------------
| EIS Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your EIS module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::prefix('eis')->middleware(['web', 'auth'])->group(function () {
    
    /**
     * Terminal Activation Web Routes
     */
    Route::prefix('terminal')->group(function () {
        
        // Show Terminal Activation Page
        Route::get('activate', [WebTerminalController::class, 'index'])
            ->name('eis.web.terminal.index');
        
        // Activate Terminal (POST)
        Route::post('activate', [WebTerminalController::class, 'activate'])
            ->name('eis.web.terminal.activate');
        
        // Deactivate Terminal (POST)
        Route::post('deactivate', [WebTerminalController::class, 'deactivate'])
            ->name('eis.web.terminal.deactivate');
        
        // Toggle Terminal Status (POST)
        Route::post('toggle', [WebTerminalController::class, 'toggle'])
            ->name('eis.web.terminal.toggle');
        
        // Get Terminal Status (AJAX)
        Route::get('status', [WebTerminalController::class, 'getStatus'])
            ->name('eis.web.terminal.status');
        
        // Regenerate Credentials (POST)
        Route::post('regenerate-credentials', [WebTerminalController::class, 'regenerateCredentials'])
            ->name('eis.web.terminal.regenerate-credentials');
    });
});