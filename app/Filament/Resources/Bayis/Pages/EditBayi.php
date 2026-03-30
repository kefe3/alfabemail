<?php

namespace App\Filament\Resources\Bayis\Pages;

use App\Filament\Resources\Bayis\BayiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBayi extends EditRecord
{
    protected static string $resource = BayiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
