<?php

namespace App\Filament\Portal\Resources\Odevler\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Ogrenci;
use App\Models\Sinif;

class OdevForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                TextInput::make('baslik')
                    ->label('Ödev Başlığı')
                    ->required()
                    ->columnSpan(12),

                Textarea::make('aciklama')
                    ->label('Açıklama')
                    ->rows(4)
                    ->columnSpan(12),

                Select::make('sinif_id')
                    ->label('Sınıf')
                    ->options(fn () => Sinif::query()
                        ->where('ogretmen_user_id', auth()->id())
                        ->orWhereHas('ogretmenler', fn($q) => $q->where('users.id', auth()->id()))
                        ->pluck('ad', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('ogrenciler', []))
                    ->columnSpan(6),

                Select::make('ogrenciler')
                    ->label('Öğrenciler (boş bırakılırsa tüm sınıf)')
                    ->multiple()
                    ->options(fn (callable $get) => $get('sinif_id')
                        ? Ogrenci::where('sinif_id', $get('sinif_id'))
                            ->with('user')
                            ->get()
                            ->mapWithKeys(fn ($o) => [$o->id => $o->user?->name ?? $o->mailbox_local_part])
                        : [])
                    ->searchable()
                    ->preload()
                    ->columnSpan(6),

                DatePicker::make('teslim_tarihi')
                    ->label('Teslim Tarihi')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->columnSpan(6),

                Toggle::make('aktif')
                    ->label('Aktif')
                    ->default(true)
                    ->columnSpan(6),

                Hidden::make('ogretmen_id')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
