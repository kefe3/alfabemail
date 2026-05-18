<?php

namespace App\Filament\Portal\Resources\Odevler\Pages;

use App\Filament\Portal\Resources\Odevler\OdevResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Ogrenci;
use Illuminate\Database\Eloquent\Model;

class CreateOdev extends CreateRecord
{
    protected static string $resource = OdevResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction(),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $selectedStudents = $data['ogrenciler'] ?? [];
        $sinifId = $data['sinif_id'];
        unset($data['ogrenciler']);

        $odev = static::getModel()::create($data);

        if (!empty($selectedStudents)) {
            $ogrenciler = Ogrenci::whereIn('id', $selectedStudents)->get();
        } else {
            $ogrenciler = Ogrenci::where('sinif_id', $sinifId)->get();
        }

        $attachData = [];
        foreach ($ogrenciler as $ogrenci) {
            $attachData[$ogrenci->id] = ['tamamlandi' => false];
        }
        $odev->ogrenciler()->attach($attachData);

        return $odev;
    }
}
