<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ExchangeRateController;

Route::prefix('currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index']); // Get all active currencies
});
// Exchange rates endpoints
Route::prefix('rates')->group(function () {
    Route::get('/', [ExchangeRateController::class, 'index']); // Get rates for period and currency pair
    Route::get('/chart', [ExchangeRateController::class, 'chart']); // Get chart data
});