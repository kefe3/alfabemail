<?php

namespace App\Filament\Portal\Resources\Sinifs;

use App\Filament\Portal\Resources\Sinifs\Pages\CreateSinif;
use App\Filament\Portal\Resources\Sinifs\Pages\EditSinif;
use App\Filament\Portal\Resources\Sinifs\Pages\ListSinifs;
use App\Filament\Portal\Resources\Sinifs\Schemas\SinifForm;
use App\Filament\Portal\Resources\Sinifs\Tables\SinifsTable;
use App\Models\Sinif;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class SinifResource extends Resource
{
    protected static ?string $model = Sinif::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function shouldSkipAuthorization(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAnyRole(['admin', 'yonetici']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return SinifForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SinifsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSinifs::route('/'),
            'create' => CreateSinif::route('/create'),
            'edit' => EditSinif::route('/{record}/edit'),
        ];
    }
}
