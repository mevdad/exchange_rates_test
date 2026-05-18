<?php

namespace App\Filament\Widgets;

use App\Models\Currency;
use App\Services\CurrencyChartDataService;
use Filament\Widgets\Widget;

class CurrencyRateChartWidget extends Widget
{
    protected string $view = 'filament.widgets.currency-rate-chart-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Currency $record = null;

    public int $period = 3;

    protected CurrencyChartDataService $dataService;

    public function boot(CurrencyChartDataService $dataService): void
    {
        $this->dataService = $dataService;
    }

    public function updatedPeriod(): void
    {
        $this->dispatch('periodChanged', period: $this->period);
    }

    public function getChartData(): array
    {
        if (! $this->record) {
            return [];
        }

        return $this->dataService->getData($this->record, $this->period);
    }
}
