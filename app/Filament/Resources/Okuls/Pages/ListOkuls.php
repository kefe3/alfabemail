<?php

namespace App\Filament\Resources\Okuls\Pages;

use App\Filament\Resources\Okuls\OkulResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOkuls extends ListRecords
{
    protected static string $resource = OkulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
