<?php

namespace App\Filament\Portal\Resources\Okuls\Pages;

use App\Filament\Portal\Resources\Okuls\OkulResource;
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
