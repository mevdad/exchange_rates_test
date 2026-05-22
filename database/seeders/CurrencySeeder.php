<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Services\ExchangeRateApiService;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    private const ACTIVE_CURRENCIES = ['USD', 'EUR', 'GBP', 'UAH'];

    public function run(): void
    {
        $apiService = app(ExchangeRateApiService::class);
        $response   = $apiService->listCurrencies();
        $list       = $response['currencies'] ?? [];

        foreach ($list as $code => $name) {
            Currency::firstOrCreate(
                ['code' => $code],
                [
                    'name'      => $name,
                    'is_active' => in_array($code, self::ACTIVE_CURRENCIES, true),
                ]
            );
        }
    }
}
