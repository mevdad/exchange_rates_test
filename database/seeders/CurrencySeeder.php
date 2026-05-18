<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use App\Services\ExchangeRateApiService;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = ['USD', 'EUR', 'GBP', 'UAH'];
        $apiService = app(ExchangeRateApiService::class);
        $response = $apiService->listCurrencies();
        $list = $response['currencies'] ?? [];

        foreach ($list as $currency=>$name) {
            if(Currency::where('code', $currency)->exists()) {
                continue; // Skip if currency already exists
            }
            Currency::factory()->create([
                'code' => $currency,
                'name' => $name,
                'is_active' => \in_array($currency, $currencies),
            ]);
        }
    }
}
