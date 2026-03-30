<?php

namespace App\Filament\Resources\Okuls;

use App\Filament\Resources\Okuls\Pages\CreateOkul;
use App\Filament\Resources\Okuls\Pages\EditOkul;
use App\Filament\Resources\Okuls\Pages\ListOkuls;
use App\Filament\Resources\Okuls\Schemas\OkulForm;
use App\Filament\Resources\Okuls\Tables\OkulsTable;
use App\Models\Okul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OkulResource extends Resource
{
    protected static ?string $model = Okul::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOkuls::route('/'),
            'create' => CreateOkul::route('/create'),
            'edit' => EditOkul::route('/{record}/edit'),
        ];
    }
}
