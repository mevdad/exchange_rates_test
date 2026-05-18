<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Currencies';

    protected static ?string $modelLabel = 'Currency';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Code')
                ->required()
                ->length(3)
                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                ->unique(ignoreRecord: true),

            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('code')
            ->actions([
                Action::make('toggle_active')
                    ->label(fn (Currency $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (Currency $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Currency $record) => $record->is_active ? 'danger' : 'success')
                    ->action(fn (Currency $record) => $record->update(['is_active' => ! $record->is_active]))
                    ->requiresConfirmation(fn (Currency $record) => $record->is_active),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('activate')
                    ->label('Activate selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($records) => $records->each->update(['is_active' => true])),

                BulkAction::make('deactivate')
                    ->label('Deactivate selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->update(['is_active' => false])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCurrencies::route('/'),
            'edit'   => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
