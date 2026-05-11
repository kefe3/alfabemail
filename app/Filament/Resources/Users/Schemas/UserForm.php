<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                    ->formatStateUsing(fn ($state) => substr($state, 0, 5) . '*****'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                TextInput::make('password')
                    ->label('Yeni Şifre (değiştirmek istemezsen boş bırak)')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
            ]);
    }
}
