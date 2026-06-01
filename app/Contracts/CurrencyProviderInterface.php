<?php

namespace App\Contracts;

interface CurrencyProviderInterface
{
    /**
     * @return array<string, string>  code → name
     *                                e.g. ['USD' => 'United States Dollar']
     */
    public function listCurrencies(): array;
}
