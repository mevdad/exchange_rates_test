<?php

namespace App\Contracts;

interface ExchangeRateProviderInterface
{
    /**
     * @return array<string, array<string, float>>  date → [pair → rate]
     *                                               e.g. ['2025-01-01' => ['USDEUR' => 0.92]]
     */
    public function getTimeframe(string $startDate, string $endDate): array;
}
