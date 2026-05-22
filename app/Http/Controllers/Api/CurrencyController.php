<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\ExchangeRateChartFormatter;
use App\Services\ExchangeRateSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    public function __construct(
        private ExchangeRateSyncService $syncService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $this->syncService->syncTodayIfMissing();
        } catch (\Exception $e) {
            Log::warning('CurrencyController: failed to sync today\'s rates', ['error' => $e->getMessage()]);
        }

        $currencies = Currency::active()
            ->with(['exchangeRatesTo' => fn ($q) => $q->where('date', '>=', now()->subDays(2)->toDateString())])
            ->get()
            ->map(function (Currency $currency) {
                $currencyData = $currency->toArray();

                if ($currency->exchangeRatesTo->isNotEmpty()) {
                    $currencyData['chart_data'] = ExchangeRateChartFormatter::formatChartData(
                        $currency->exchangeRatesTo,
                        'USD',
                        $currency->code,
                        3
                    );
                }

                return $currencyData;
            });

        return response()->json([
            'success' => true,
            'data'    => $currencies,
        ]);
    }
}
