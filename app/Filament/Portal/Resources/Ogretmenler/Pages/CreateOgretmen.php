<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Pages;

use App\Filament\Portal\Resources\Ogretmenler\OgretmenlerResource;
use App\Models\Okul;
use App\Services\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateOgretmen extends CreateRecord
{
    protected static string $resource = OgretmenlerResource::class;

    protected function mutateFormData(array $data): array
    {
        unset($data['password_confirmation']);

        $userOkulId = auth()->user()?->okul?->id;
        if ($userOkulId) {
            $data['okul_id'] = $userOkulId;
        }

        $data['password'] = Hash::make($data['password']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole('ogretmen');

        $sinifIds = $this->form->getState()['sinif_ids'] ?? [];
        if (!empty($sinifIds)) {
            $this->record->ogretmen_sinifler_pivot()->sync($sinifIds);
        }

        ActivityLogger::log('created', 'user', [
            'target_id' => $this->record->id,
            'target_name' => $this->record->name . ' (' . $this->record->email . ')',
            'description' => 'Öğretmen oluşturuldu: ' . $this->record->name,
        ]);
    }
}