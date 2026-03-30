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

class SinifResource extends Resource
{
    protected static ?string $model = Sinif::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
