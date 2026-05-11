<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ad')
                    ->label('Sponsor Adı')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('sponsors')
                    ->disk('public'),
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->maxLength(500),
                TextInput::make('sira')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0),
                Toggle::make('aktif')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}