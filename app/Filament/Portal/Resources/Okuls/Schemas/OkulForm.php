<?php

namespace App\Filament\Portal\Resources\Okuls\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OkulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                TextInput::make('ad')
                    ->label('Okul Adı')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12),

                TextInput::make('telefon')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(255)
                    ->columnSpan(12),

                TextInput::make('adres')
                    ->label('Adres')
                    ->maxLength(500)
                    ->columnSpan(12),

                TextInput::make('ulke')
                    ->label('Ülke')
                    ->default('Türkiye')
                    ->columnSpan(6),

                TextInput::make('sehir')
                    ->label('Şehir')
                    ->columnSpan(6),

                TextInput::make('ilce')
                    ->label('İlçe')
                    ->columnSpan(6),

                TextInput::make('mahalle')
                    ->label('Mahalle')
                    ->columnSpan(6),

                Radio::make('yonetici_tipi')
                    ->label('Yönetici Seçimi')
                    ->options([
                        'mevcut' => 'Mevcut Yönetici',
                        'yeni' => 'Yeni Yönetici Oluştur',
                    ])
                    ->default('mevcut')
                    ->inline()
                    ->live()
                    ->columnSpan(12),

                Select::make('yonetici_user_id')
                    ->label('Yönetici')
                    ->relationship('yonetici', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'yonetici')))
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' - ' . $record->email)
                    ->hidden(fn ($get) => $get('yonetici_tipi') !== 'mevcut')
                    ->searchable()
                    ->preload()
                    ->columnSpan(12),

                TextInput::make('yonetici_ad_soyad')
                    ->label('Mevcut Yönetici Adı')
                    ->disabled()
                    ->visible(fn ($get) => $get('yonetici_tipi') === 'mevcut' && !empty($get('yonetici_user_id')))
                    ->columnSpan(6),

                TextInput::make('yonetici_email')
                    ->label('Mevcut Yönetici E-posta')
                    ->disabled()
                    ->visible(fn ($get) => $get('yonetici_tipi') === 'mevcut' && !empty($get('yonetici_user_id')))
                    ->columnSpan(6),

                TextInput::make('yonetici_ad_soyad')
                    ->label('Yönetici Adı Soyadı')
                    ->visible(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->required(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->maxLength(255)
                    ->columnSpan(6),

                TextInput::make('yonetici_email')
                    ->label('Yönetici E-posta')
                    ->email()
                    ->visible(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->required(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->maxLength(255)
                    ->rule(function ($get) {
                        return function ($attribute, $value, $fail) use ($get) {
                            if ($get('yonetici_tipi') === 'yeni' && \App\Models\User::where('email', $value)->exists()) {
                                $fail('Bu e-posta adresi zaten kullanılıyor. Lütfen "Mevcut Yönetici" seçeneğini kullanın veya farklı bir e-posta girin.');
                            }
                        };
                    })
                    ->columnSpan(6),

                TextInput::make('yonetici_sifre')
                    ->label('Şifre')
                    ->password()
                    ->visible(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->required(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->minLength(6)
                    ->columnSpan(6),

                TextInput::make('yonetici_sifre_tekrar')
                    ->label('Şifre Tekrar')
                    ->password()
                    ->visible(fn ($get) => $get('yonetici_tipi') === 'yeni')
                    ->same('yonetici_sifre')
                    ->columnSpan(6),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->columnSpan(12),
            ]);
    }
}