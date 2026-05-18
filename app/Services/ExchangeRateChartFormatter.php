<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ExchangeRateChartFormatter
{
    /**
     * Format exchange rates data for chart display
     * 
     * @param Collection $rates Collection of ExchangeRate objects with rate and date
     * @param string $fromCode Source currency code
     * @param string $toCode Target currency code
     * @param int $days Period in days
     * @return array Formatted data with series, categories and statistics
     */
    public static function formatChartData(Collection $rates, string $fromCode, string $toCode, int $days): array
    {
        $dates = [];
        $values = [];

        foreach ($rates as $rate) {
            $dates[] = $rate->date->format('M d');
            $values[] = (float) $rate->rate;
        }

        // Calculate statistics
        $minRate = min($values);
        $maxRate = max($values);
        $currentRate = end($values);
        $previousRate = \count($values) > 1 ? $values[0] : $currentRate;
        $change = $currentRate - $previousRate;
        $changePercent = $previousRate != 0 ? ($change / $previousRate) * 100 : 0;

        return [
            'series' => [
                [
                    'name' => "{$fromCode} to {$toCode}",
                    'data' => $values,
                ],
            ],
            'categories' => $dates,
            'currency_pair' => "{$fromCode}/{$toCode}",
            'period_days' => $days,
            'statistics' => [
                'current' => $currentRate,
                'change' => round($change, 8),
                'change_percent' => round($changePercent, 2),
                'min' => $minRate,
                'max' => $maxRate,
            ],
        ];
    }
}
