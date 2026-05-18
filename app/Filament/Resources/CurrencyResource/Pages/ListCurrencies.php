<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use App\Models\Currency;
use Database\Seeders\CurrencySeeder;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync Currencies')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Sync currencies from API?')
                ->modalDescription('New currencies from the exchange rate API will be added. Existing ones will not be changed.')
                ->modalSubmitActionLabel('Sync')
                ->action(function () {
                    try {
                        app(CurrencySeeder::class)->run();
                        Notification::make()
                            ->title('Sync completed')
                            ->body('Currencies have been synced from the API.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Sync failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(Currency::where('is_active', true)->count()),

            'all' => Tab::make('All')
                ->badge(Currency::count()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'active';
    }
}
