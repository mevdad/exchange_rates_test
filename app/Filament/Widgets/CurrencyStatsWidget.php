<?php

namespace App\Filament\Widgets;

use App\Models\Currency;
use App\Services\CurrencyChartDataService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CurrencyStatsWidget extends StatsOverviewWidget
{
    public ?Currency $record = null;

    public int $period = 3;

    protected int|string|array $columnSpan = 'full';

    protected CurrencyChartDataService $dataService;

    public function boot(CurrencyChartDataService $dataService): void
    {
        $this->dataService = $dataService;
    }

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $data = $this->dataService->getData($this->record, $this->period);

        if (empty($data)) {
            return [];
        }

        $stats    = $data['statistics'];
        $positive = $stats['change'] >= 0;
        $period   = $this->period > 0 ? "{$this->period}d" : 'all time';

        return [
            Stat::make('Current Rate', number_format($stats['current'], 3))
                ->description('1 USD = ' . number_format($stats['current'], 3) . ' ' . $this->record->code)
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make("Change ({$period})", ($positive ? '+' : '') . number_format($stats['change'], 3))
                ->description($stats['change_percent'] . '% from ' . $period . ' ago')
                ->descriptionIcon($positive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($positive ? 'success' : 'danger'),

            Stat::make('High / Low', number_format($stats['max'], 3) . ' / ' . number_format($stats['min'], 3))
                ->description('Range over ' . $period)
                ->descriptionIcon('heroicon-m-arrows-up-down')
                ->color('warning'),
        ];
    }

    protected function getListeners(): array
    {
        return [
            'periodChanged' => 'onPeriodChanged',
        ];
    }

    public function onPeriodChanged(int $period): void
    {
        $this->period = $period;
    }
}
