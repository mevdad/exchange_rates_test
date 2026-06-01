<?php

namespace Database\Seeders;

use App\Contracts\CurrencyProviderInterface;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    private const ACTIVE_CURRENCIES = ['USD', 'EUR', 'GBP', 'UAH'];

    public function run(): void
    {
        $provider = app(CurrencyProviderInterface::class);
        $list     = $provider->listCurrencies();

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
