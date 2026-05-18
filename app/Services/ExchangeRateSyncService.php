<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExchangeRateSyncService
{
    public function __construct(
        private ExchangeRateApiService $apiService,
    ) {}

    /**
     * Sync today's rates only if they are not yet in the database.
     * Returns true if sync was performed, false if data already existed.
     */
    public function syncTodayIfMissing(): bool
    {
        $today = Carbon::today()->toDateString();

        if (ExchangeRate::where('date', $today)->exists()) {
            return false;
        }

        $this->syncTimeframe($today, $today);

        return true;
    }

    public function syncTimeframe(string $startDate, string $endDate): array
    {
        $response = $this->apiService->getTimeframe($startDate, $endDate);

        return $this->importQuotes($response['quotes'] ?? []);
    }

    /**
     * @param array<string, array<string, float>> $quotes  date → [pair → rate]
     * @return array{created: int, skipped: int}
     */
    public function importQuotes(array $quotes): array
    {
        $created = 0;
        $skipped = 0;

        foreach ($quotes as $date => $rates) {
            foreach ($rates as $pair => $rate) {
                $fromCode = substr($pair, 0, 3);
                $toCode = substr($pair, 3);

                $fromCurrency = Currency::firstWhere('code', $fromCode);
                $toCurrency = Currency::firstWhere('code', $toCode);

                if ($fromCurrency && $toCurrency) {
                    ExchangeRate::create([
                        'from_currency_id' => $fromCurrency->id,
                        'to_currency_id' => $toCurrency->id,
                        'rate' => $rate,
                        'date' => Carbon::parse($date),
                    ]);
                    $created++;
                } else {
                    Log::warning("ExchangeRateSyncService: currency pair not found: {$fromCode}/{$toCode}");
                    $skipped++;
                }
            }
        }

        return ['created' => $created, 'skipped' => $skipped];
    }
}
