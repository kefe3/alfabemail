<?php

namespace App\Filament\Resources\Bayis\Pages;

use App\Filament\Resources\Bayis\BayiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBayis extends ListRecords
{
    protected static string $resource = BayiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
