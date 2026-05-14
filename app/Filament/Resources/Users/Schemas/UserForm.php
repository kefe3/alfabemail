<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Ogrenci;
use App\Models\Okul;
use App\Models\Sinif;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCreate = fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord;
        $ogrenciRoleId = Role::where('name', 'ogrenci')->value('id');
        $isOgrenci = fn (callable $get) => $ogrenciRoleId && in_array($ogrenciRoleId, $get('roles') ?? []);
        $yoneticiRoleId = Role::where('name', 'yonetici')->value('id');
        $isYonetici = fn (callable $get) => $yoneticiRoleId && in_array($yoneticiRoleId, $get('roles') ?? []);
        $ogretmenRoleId = Role::where('name', 'ogretmen')->value('id');
        $isOgretmen = fn (callable $get) => $ogretmenRoleId && in_array($ogretmenRoleId, $get('roles') ?? []);
        $veliRoleId = Role::where('name', 'veli')->value('id');
        $isVeli = fn (callable $get) => $veliRoleId && in_array($veliRoleId, $get('roles') ?? []);

        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(255)
                    ->hidden(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get)),

                TextInput::make('first_name')
                    ->label('Ad')
                    ->required(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get))
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Soyad')
                    ->required(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get))
                    ->maxLength(255),

                TextInput::make('nickname')
                    ->label('Rumuz (Nickname)')
                    ->suffix('@alfabe.co')
                    ->helperText('Boş bırakılırsa ad.soyad kullanılır')
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get)),

                Select::make('sinif_id')
                    ->label('Sınıf')
                    ->options(fn () => Sinif::pluck('ad', 'id'))
                    ->searchable()
                    ->preload()
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get)),

                TextInput::make('anne_email')
                    ->label('Anne E-posta')
                    ->email()
                    ->nullable()
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get)),

                TextInput::make('baba_email')
                    ->label('Baba E-posta')
                    ->email()
                    ->nullable()
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get)),

                TextInput::make('email')
                    ->label('Email address')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($livewire) => !$isCreate($livewire))
                    ->hidden(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->formatStateUsing(fn ($state) => substr($state, 0, 5) . '*****'),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->live(),

                Select::make('okul_id')
                    ->label('Okul')
                    ->options(fn () => Okul::pluck('ad', 'id'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('ad')
                            ->label('Okul Adı')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Okul::create([
                            'ad' => $data['ad'],
                            'is_active' => true,
                        ])->id;
                    })
                    ->createOptionModalHeading('Yeni Okul Oluştur')
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || (!$isYonetici($get) && !$isOgretmen($get))),

                Select::make('sinif_ids')
                    ->label('Sınıflar')
                    ->multiple()
                    ->options(fn (callable $get) => $get('okul_id') ? Sinif::where('okul_id', $get('okul_id'))->pluck('ad', 'id') : [])
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('ad')->label('Sınıf Adı')->required()->maxLength(255),
                        Hidden::make('okul_id')
                            ->default(fn ($livewire) => data_get($livewire, 'data.okul_id')),
                    ])
                    ->createOptionUsing(function (array $data) {
                        if (!$data['okul_id']) {
                            Notification::make()->title('Önce okul seçin')->warning()->send();
                            return null;
                        }
                        return Sinif::create(['ad' => $data['ad'], 'okul_id' => $data['okul_id']])->id;
                    })
                    ->createOptionModalHeading('Yeni Sınıf Oluştur')
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgretmen($get)),

                Select::make('ogrenci_ids')
                    ->label('Öğrenciler')
                    ->multiple()
                    ->options(fn () => Ogrenci::join('users', 'ogrenciler.user_id', '=', 'users.id')
                        ->pluck('users.name', 'ogrenciler.id'))
                    ->searchable()
                    ->preload()
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isVeli($get)),

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
