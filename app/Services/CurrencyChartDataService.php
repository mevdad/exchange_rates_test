<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;

class CurrencyChartDataService
{
    // Static cache lives for the duration of one PHP request.
    // Both widgets on the same page hit the same key and share one query result.
    private static array $cache = [];

    public function getData(Currency $record, int $period): array
    {
        $key = "{$record->id}_{$period}";

        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        $usd = Currency::where('code', 'USD')->first();

        if (! $usd) {
            return static::$cache[$key] = [];
        }

        $query = ExchangeRate::forCurrencyPair($usd->id, $record->id)->orderBy('date');

        if ($period > 0) {
            $query->forDateRange(
                Carbon::now()->subDays($period - 1)->startOfDay(),
                Carbon::now()->endOfDay()
            );
        }

        $rates = $query->select('rate', 'date')->get();

        if ($rates->isEmpty()) {
            return static::$cache[$key] = [];
        }

        return static::$cache[$key] = ExchangeRateChartFormatter::formatChartData(
            $rates,
            'USD',
            $record->code,
            $period
        );
    }

    public static function clearCache(): void
    {
        static::$cache = [];
    }
}
