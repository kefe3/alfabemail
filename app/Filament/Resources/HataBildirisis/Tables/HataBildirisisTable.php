<?php

namespace App\Filament\Resources\HataBildirisis\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HataBildirisisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->width(40),
                TextColumn::make('konu')
                    ->label('Konu')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('ad_soyad')
                    ->label('Ad Soyad')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('aciklama')
                    ->label('Açıklama')
                    ->limit(80)
                    ->html()
                    ->toggleable(),
                ImageColumn::make('ekran_goruntusu')
                    ->label('Ekran Görüntüsü')
                    ->getStateUsing(fn ($record): ?string => $record->ekran_goruntusu ? '/storage/' . $record->ekran_goruntusu : null)
                    ->width(120)
                    ->toggleable(),
                ToggleColumn::make('cozuldu_mu')
                    ->label('Çözüldü'),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
