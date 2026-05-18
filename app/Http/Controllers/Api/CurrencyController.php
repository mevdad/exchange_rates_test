<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Services\ExchangeRateChartFormatter;
use App\Services\ExchangeRateSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController
{
    public function __construct(
        private ExchangeRateSyncService $syncService,
    ) {}

    public function index(): JsonResponse
    {
        $this->syncService->syncTodayIfMissing();
        $currencies = Currency::active()
            ->with('exchangeRatesTo')
            ->get()
            ->map(function (Currency $currency) {
                $currencyData = $currency->toArray();
                
                // Format exchange rates as chart data
                if ($currency->exchangeRatesTo->isNotEmpty()) {
                    $chartData = ExchangeRateChartFormatter::formatChartData(
                        $currency->exchangeRatesTo,
                        'USD',  // Assuming source is USD
                        $currency->code,
                        3  // Last 3 days
                    );
                    
                    $currencyData['chart_data'] = $chartData;
                }
                
                return $currencyData;
            });

        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }
}
