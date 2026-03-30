<?php

namespace App\Filament\Portal\Resources\Sinifs\Pages;

use App\Filament\Portal\Resources\Sinifs\SinifResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSinifs extends ListRecords
{
    protected static string $resource = SinifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
