<?php

namespace App\Filament\Resources\Okuls\Pages;

use App\Filament\Resources\Okuls\OkulResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOkul extends EditRecord
{
    protected static string $resource = OkulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
