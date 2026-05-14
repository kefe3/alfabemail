<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Schemas;

use App\Models\Okul;
use App\Models\Sinif;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class OgretmenForm
{
    public static function configure(Schema $schema): Schema
    {
        $userOkulId = auth()->user()?->okul?->id;

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

                Select::make('sinif_ids')
                    ->label('Sınıflar')
                    ->multiple()
                    ->options(fn () => $userOkulId ? Sinif::where('okul_id', $userOkulId)->pluck('ad', 'id') : [])
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('ad')->label('Sınıf Adı')->required()->maxLength(255),
                        Hidden::make('okul_id')
                            ->default($userOkulId),
                    ])
                    ->createOptionUsing(function (array $data) {
                        if (!$data['okul_id']) {
                            Notification::make()->title('Okul bulunamadı')->warning()->send();
                            return null;
                        }
                        return Sinif::create(['ad' => $data['ad'], 'okul_id' => $data['okul_id']])->id;
                    })
                    ->createOptionModalHeading('Yeni Sınıf Oluştur'),
            ]);
    }
}