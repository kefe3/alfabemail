<?php

namespace App\Filament\Portal\Resources\Odevler\Pages;

use App\Filament\Portal\Resources\Odevler\OdevResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListOdevler extends ListRecords
{
    protected static string $resource = OdevResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Yeni Ödev')
                ->url(fn () => static::getResource()::getUrl('create'))
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen'])),
        ];
    }
}
