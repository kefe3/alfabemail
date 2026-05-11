<?php

namespace App\Filament\Portal\Resources\Sinifs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\User;

class SinifForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                TextInput::make('ad')
                    ->label('Sınıf Adı')
                    ->placeholder('Örn: 5-A')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12),

                Select::make('okul_id')
                    ->label('Okul')
                    ->relationship('okul', 'ad')
                    ->required()
                    ->default(fn () => auth()->user()?->okul?->id)
                    ->columnSpan(12),

                Select::make('ogretmenler')
                    ->label('Öğretmenler')
                    ->options(function () {
                        return \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'ogretmen'))
                            ->pluck('name', 'id');
                    })
                    ->multiple()
                    ->searchable()
                    ->columnSpan(12),
            ]);
    }
}