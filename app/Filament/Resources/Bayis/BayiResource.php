<?php

namespace App\Filament\Resources\Bayis;

use App\Filament\Resources\Bayis\Pages\CreateBayi;
use App\Filament\Resources\Bayis\Pages\EditBayi;
use App\Filament\Resources\Bayis\Pages\ListBayis;
use App\Filament\Resources\Bayis\Schemas\BayiForm;
use App\Filament\Resources\Bayis\Tables\BayisTable;
use App\Models\Bayi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BayiResource extends Resource
{
    protected static ?string $model = Bayi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BayiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BayisTable::configure($table);
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
            'index' => ListBayis::route('/'),
            'create' => CreateBayi::route('/create'),
            'edit' => EditBayi::route('/{record}/edit'),
        ];
    }
}
