<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateChartFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeRateController
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'string|size:3',
            'to'   => 'required|string|size:3',
            'days' => 'integer|in:0,3,5,7,10,14',
        ]);

        $from = $validated['from'] ?? 'USD';
        $to   = $validated['to'];
        $days = $validated['days'] ?? 3;

        try {
            $fromCurrency = Currency::where('code', $from)->first();
            $toCurrency   = Currency::where('code', $to)->first();

            if (! $fromCurrency || ! $toCurrency) {
                return response()->json(['success' => false, 'message' => 'One or both currencies not found'], 404);
            }

            $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();

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
                'data'    => [
                    'from'        => $from,
                    'to'          => $to,
                    'period_days' => $days,
                    'rates'       => $rates,
                    'start_date'  => $startDate->toDateString(),
                    'end_date'    => $endDate->toDateString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching exchange rates', 'error' => $e->getMessage()], 500);
        }
    }

    public function chart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'string|size:3',
            'to'   => 'required|string|size:3',
            'days' => 'integer|in:0,3,5,7,10,14',
        ]);

        $from = $validated['from'] ?? 'USD';
        $to   = $validated['to'];
        $days = $validated['days'] ?? 3;

        try {
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
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching chart data', 'error' => $e->getMessage()], 500);
        }
    }

    private function baseQuery(int $fromId, int $toId): Builder
    {
        return ExchangeRate::forCurrencyPair($fromId, $toId)
            ->orderBy('date')
            ->select('rate', 'date');
    }
}
