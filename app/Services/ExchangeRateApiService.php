<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateApiService
{
    protected string $accessKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->accessKey = config('services.exchangerate.key');
        $this->baseUrl   = rtrim(config('services.exchangerate.url'), '/');
    }

    public function getLive(string $base = 'USD', array $symbols = []): array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/live", [
                    'access_key' => $this->accessKey,
                    'base'       => $base,
                    'symbols'    => implode(',', $symbols),
                ])
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (getLive)', ['message' => $e->getMessage(), 'base' => $base]);
            throw $e;
        }
    }

    public function getHistorical(string $date): array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/historical", [
                    'access_key' => $this->accessKey,
                    'date'       => $date,
                ])
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (getHistorical)', ['message' => $e->getMessage(), 'date' => $date]);
            throw $e;
        }
    }

    public function getTimeframe(string $startDate, string $endDate): array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/timeframe", [
                    'access_key' => $this->accessKey,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ])
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (getTimeframe)', [
                'message'   => $e->getMessage(),
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ]);
            throw $e;
        }
    }

    public function listCurrencies(): array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/list", ['access_key' => $this->accessKey])
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (listCurrencies)', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
