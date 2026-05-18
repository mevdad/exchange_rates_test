<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ExchangeRateApiService
{
    protected string $accessKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->accessKey = config('services.exchangerate.key');
        $this->baseUrl = rtrim(config('services.exchangerate.url'), '/');
    }

    /**
     * Fetch the most recent exchange rates
     *
     * @param string $base Base currency code (default: EUR)
     * @param array $symbols Target currency codes
     * @return array Exchange rates data
     */
    public function getLive(string $base = 'USD', array $symbols = []): array
    {
        try {
            $params = [
                'access_key' => $this->accessKey,
                'base' => $base,
                'symbols' => implode(',', $symbols)
            ];

         
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/live", $params)
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (getLive)', [
                'message' => $e->getMessage(),
                'base' => $base,
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve historical exchange rates for a specific date
     *
     * @param string $date Date in format YYYY-MM-DD
     * @return array Historical exchange rates
     */
    public function getHistorical(string $date): array
    {
        try {
            $params = [
                'access_key' => $this->accessKey,
                'date' => $date
            ];

            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/historical", $params)
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (getHistorical)', [
                'message' => $e->getMessage(),
                'date' => $date
            ]);
            throw $e;
        }
    }

    /**
     * Fetch exchange rate data over a range of dates (timeframe)
     *
     * @param string $startDate Start date in format YYYY-MM-DD
     * @param string $endDate End date in format YYYY-MM-DD
     * @return array Timeframe exchange rates
     */
    public function getTimeframe(
        string $startDate,
        string $endDate
    ): array {
        try {
            $params = [
                'access_key' => $this->accessKey,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/timeframe", $params)
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (getTimeframe)', [
                'message' => $e->getMessage(),
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a list of all supported currencies
     *
     * @return array List of supported currencies
     */
    public function listCurrencies(): array
    {
        try {
            $params = [
                'access_key' => $this->accessKey,
            ];

            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/list", $params)
                ->throw()
                ->json();

            return $response;
        } catch (Exception $e) {
            Log::error('ExchangeRate API Error (listCurrencies)', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}


