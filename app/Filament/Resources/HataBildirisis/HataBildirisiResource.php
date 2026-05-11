<?php

namespace App\Filament\Resources\HataBildirisis;

use App\Filament\Resources\HataBildirisis\Pages\EditHataBildirisi;
use App\Filament\Resources\HataBildirisis\Pages\ListHataBildirisis;
use App\Filament\Resources\HataBildirisis\Schemas\HataBildirisiForm;
use App\Filament\Resources\HataBildirisis\Tables\HataBildirisisTable;
use App\Models\HataBildirisi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HataBildirisiResource extends Resource
{
    protected static ?string $model = HataBildirisi::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?string $navigationLabel = 'Hata Bildirisi';

    protected static ?string $label = 'Hata Bildirisi';

    protected static bool $shouldRegisterNavigation = true;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return HataBildirisiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HataBildirisisTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHataBildirisis::route('/'),
            'edit' => EditHataBildirisi::route('/{record}/edit'),
        ];
    }
}
