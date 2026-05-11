<?php

namespace App\Filament\Resources\HataBildirisis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class HataBildirisiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Placeholder::make('created_at')
                    ->label('Gönderilme Tarihi')
                    ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),
                TextInput::make('ad_soyad')
                    ->label('Ad Soyad')
                    ->disabled(),
                TextInput::make('email')
                    ->label('E-posta')
                    ->disabled(),
                TextInput::make('konu')
                    ->label('Konu')
                    ->disabled()
                    ->columnSpanFull(),
                Textarea::make('aciklama')
                    ->label('Açıklama')
                    ->disabled()
                    ->rows(8)
                    ->columnSpanFull(),
                Placeholder::make('ekran_goruntusu')
                    ->label('Ekran Görüntüsü')
                    ->content(fn ($record) => $record?->ekran_goruntusu
                        ? '<img src="/storage/' . $record->ekran_goruntusu . '" style="max-width:100%;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);" />'
                        : 'Ekran görüntüsü yok')
                    ->html()
                    ->columnSpanFull(),
                TextInput::make('sayfa')
                    ->label('Sayfa')
                    ->disabled(),
                TextInput::make('tarayici')
                    ->label('Tarayıcı')
                    ->disabled(),
                Toggle::make('cozuldu_mu')
                    ->label('Çözüldü mü?')
                    ->inline(false),
            ]);
    }
}
