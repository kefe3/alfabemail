<?php

namespace App\Filament\Portal\Resources\Okuls;

use App\Filament\Portal\Resources\Okuls\Pages\CreateOkul;
use App\Filament\Portal\Resources\Okuls\Pages\EditOkul;
use App\Filament\Portal\Resources\Okuls\Pages\ListOkuls;
use App\Filament\Portal\Resources\Okuls\Schemas\OkulForm;
use App\Filament\Portal\Resources\Okuls\Tables\OkulsTable;
use App\Models\Okul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OkulResource extends Resource
{
    protected static ?string $model = Okul::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return OkulForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OkulsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOkuls::route('/'),
            'edit' => EditOkul::route('/{record}/edit'),
        ];
    }
}