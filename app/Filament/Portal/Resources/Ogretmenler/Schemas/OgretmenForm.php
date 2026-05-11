<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Okul;

class OgretmenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->unique('users', 'email'),

                TextInput::make('password')
                    ->label('Şifre')
                    ->password()
                    ->required()
                    ->minLength(6)
                    ->default('Demo123!'),

                TextInput::make('password_confirmation')
                    ->label('Şifre Tekrar')
                    ->password()
                    ->required()
                    ->same('password'),
            ]);
    }
}