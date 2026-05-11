<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\ActivityLogger;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::created($this->record, 'Kullanıcı oluşturuldu: ' . $this->record->name);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Oluştur')
                ->submit('create'),
            Action::make('cancel')
                ->label('İptal')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
