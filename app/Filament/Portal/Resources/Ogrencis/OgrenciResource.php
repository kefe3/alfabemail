<?php

namespace App\Filament\Portal\Resources\Ogrencis;

use App\Filament\Portal\Resources\Ogrencis\Pages\CreateOgrenci;
use App\Filament\Portal\Resources\Ogrencis\Pages\EditOgrenci;
use App\Filament\Portal\Resources\Ogrencis\Pages\ListOgrencis;
use App\Filament\Portal\Resources\Ogrencis\Schemas\OgrenciForm;
use App\Filament\Portal\Resources\Ogrencis\Tables\OgrencisTable;
use App\Models\Ogrenci;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OgrenciResource extends Resource
{
    protected static ?string $model = Ogrenci::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OgrenciForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OgrencisTable::configure($table);
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
            'index' => ListOgrencis::route('/'),
            'create' => CreateOgrenci::route('/create'),
            'edit' => EditOgrenci::route('/{record}/edit'),
        ];
    }
}
