<?php

namespace App\Filament\Portal\Resources\Ogrencis\Pages;

use App\Filament\Portal\Resources\Ogrencis\OgrenciResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListOgrencis extends ListRecords
{
    protected static string $resource = OgrenciResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_student')
                ->label('Yeni Öğrenci Ekle')
                ->url(fn () => static::getResource()::getUrl('create'))
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false),
        ];
    }
}
