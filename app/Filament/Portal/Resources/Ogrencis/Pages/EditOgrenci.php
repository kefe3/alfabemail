<?php

namespace App\Filament\Portal\Resources\Ogrencis\Pages;

use App\Filament\Portal\Resources\Ogrencis\OgrenciResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOgrenci extends EditRecord
{
    protected static string $resource = OgrenciResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
