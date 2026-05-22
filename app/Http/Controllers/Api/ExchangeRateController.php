<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRatesRequest;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateChartFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ExchangeRateController extends Controller
{
    public function index(GetRatesRequest $request): JsonResponse
    {
        $from = $request->fromCurrency();
        $to   = $request->toCurrency();
        $days = $request->periodDays();

        $fromCurrency = Currency::where('code', $from)->first();
        $toCurrency   = Currency::where('code', $to)->first();

        if (! $fromCurrency || ! $toCurrency) {
            return response()->json(['success' => false, 'message' => 'One or both currencies not found'], 404);
        }

        $query = $this->baseQuery($fromCurrency->id, $toCurrency->id);

        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        if ($days > 0) {
            $query->forDateRange($startDate, $endDate);
        }

        $rates = $query->get();

        if ($rates->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No exchange rates found for this period'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'from'        => $from,
                'to'          => $to,
                'period_days' => $days,
                'rates'       => $rates,
                'start_date'  => $startDate->toDateString(),
                'end_date'    => $endDate->toDateString(),
            ],
        ]);
    }

    public function chart(GetRatesRequest $request): JsonResponse
    {
        $from = $request->fromCurrency();
        $to   = $request->toCurrency();
        $days = $request->periodDays();

        $fromCurrency = Currency::where('code', $from)->first();
        $toCurrency   = Currency::where('code', $to)->first();

        if (! $fromCurrency || ! $toCurrency) {
            return response()->json(['success' => false, 'message' => 'One or both currencies not found'], 404);
        }

        $query = $this->baseQuery($fromCurrency->id, $toCurrency->id);

        if ($days > 0) {
            $query->forDateRange(
                Carbon::now()->subDays($days - 1)->startOfDay(),
                Carbon::now()->endOfDay()
            );
        }

        $rates = $query->get();

        if ($rates->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No exchange rates found for this period'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => ExchangeRateChartFormatter::formatChartData($rates, $from, $to, $days),
        ]);
    }

    private function baseQuery(int $fromId, int $toId): Builder
    {
        return ExchangeRate::forCurrencyPair($fromId, $toId)
            ->orderBy('date')
            ->select('rate', 'date');
    }
}
