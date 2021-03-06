<?php

use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function() {
    Route::post('init', [Controllers\UserController::class, 'init']);
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('wallet', [Controllers\WalletController::class, 'enableWallet']);
        Route::get('wallet', [Controllers\WalletController::class, 'show']);
        Route::post('wallet/deposit', [Controllers\WalletController::class, 'deposit']);
        Route::post('wallet/withdrawals', [Controllers\WalletController::class, 'withdraw']);
        Route::patch('wallet', [Controllers\WalletController::class, 'disableWallet']);
    });
});
