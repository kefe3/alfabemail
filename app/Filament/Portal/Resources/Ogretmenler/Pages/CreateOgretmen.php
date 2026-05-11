<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Pages;

use App\Filament\Portal\Resources\Ogretmenler\OgretmenlerResource;
use App\Services\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateOgretmen extends CreateRecord
{
    protected static string $resource = OgretmenlerResource::class;

    protected function mutateFormData(array $data): array
    {
        unset($data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole('ogretmen');
        
        ActivityLogger::log('created', 'user', [
            'target_id' => $this->record->id,
            'target_name' => $this->record->name . ' (' . $this->record->email . ')',
            'description' => 'Öğretmen oluşturuldu: ' . $this->record->name,
        ]);
    }
}