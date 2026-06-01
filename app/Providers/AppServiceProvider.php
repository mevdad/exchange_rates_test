<?php

namespace App\Providers;

use App\Contracts\CurrencyProviderInterface;
use App\Contracts\ExchangeRateProviderInterface;
use App\Services\ExchangeRateApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ExchangeRateProviderInterface::class, ExchangeRateApiService::class);
        $this->app->bind(CurrencyProviderInterface::class, ExchangeRateApiService::class);
    }

    public function boot(): void
    {
        //
    }
}
