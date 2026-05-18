<?php

namespace App\Filament\Portal\Resources\Odevler\Pages;

use App\Filament\Portal\Resources\Odevler\OdevResource;
use App\Models\Ogrenci;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOdev extends EditRecord
{
    protected static string $resource = OdevResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $odev = $this->getRecord();
        $data['ogrenciler'] = $odev->ogrenciler()->pluck('ogrenciler.id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $selectedStudents = $data['ogrenciler'] ?? [];
        unset($data['ogrenciler']);

        $odev = $this->getRecord();

        if (!empty($selectedStudents)) {
            $ogrenciler = Ogrenci::whereIn('id', $selectedStudents)->get();
        } else {
            $ogrenciler = Ogrenci::where('sinif_id', $data['sinif_id'] ?? $odev->sinif_id)->get();
        }

        $syncData = [];
        foreach ($ogrenciler as $ogrenci) {
            $existing = $odev->ogrenciler()->where('ogrenci_id', $ogrenci->id)->first();
            $syncData[$ogrenci->id] = [
                'tamamlandi' => $existing ? (bool) $existing->pivot->tamamlandi : false,
                'tamamlanma_tarihi' => $existing ? $existing->pivot->tamamlanma_tarihi : null,
            ];
        }
        $odev->ogrenciler()->sync($syncData);

        return $data;
    }
}
