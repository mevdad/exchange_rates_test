<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use App\Filament\Widgets\CurrencyRateChartWidget;
use App\Filament\Widgets\CurrencyStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CurrencyStatsWidget::class,
            CurrencyRateChartWidget::class,
        ];
    }
}
