<?php

namespace App\Filament\Resources\Sponsors\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SponsorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(40),
                TextColumn::make('ad')
                    ->label('Ad')
                    ->searchable(),
                TextColumn::make('website')
                    ->label('Website')
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab(),
                TextColumn::make('sira')
                    ->label('Sıra')
                    ->sortable(),
                ToggleColumn::make('aktif')
                    ->label('Aktif'),
            ])
            ->defaultSort('sira')
            ->paginated([10, 25, 50]);
    }
}