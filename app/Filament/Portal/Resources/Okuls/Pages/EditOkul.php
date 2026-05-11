<?php

namespace App\Filament\Portal\Resources\Okuls\Pages;

use App\Filament\Portal\Resources\Okuls\OkulResource;
use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOkul extends EditRecord
{
    protected static string $resource = OkulResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['yonetici_tipi'] = 'mevcut';
        
        if ($this->record->yonetici) {
            $data['yonetici_ad_soyad'] = $this->record->yonetici->name;
            $data['yonetici_email'] = $this->record->yonetici->email;
        } else {
            $data['yonetici_ad_soyad'] = '';
            $data['yonetici_email'] = '';
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $yoneticiTipi = $data['yonetici_tipi'] ?? 'mevcut';

        unset($data['yonetici_sifre_tekrar']);

        if ($yoneticiTipi === 'yeni' && !empty($data['yonetici_ad_soyad']) && !empty($data['yonetici_email'])) {
            $sifre = !empty($data['yonetici_sifre']) ? $data['yonetici_sifre'] : 'Demo123!';
            
            $user = User::create([
                'name' => $data['yonetici_ad_soyad'],
                'email' => $data['yonetici_email'],
                'password' => bcrypt($sifre),
                'is_active' => true,
            ]);
            $user->assignRole('yonetici');
            $data['yonetici_user_id'] = $user->id;
            
            ActivityLogger::log('created', 'user', [
                'target_id' => $user->id,
                'target_name' => $user->name . ' (' . $user->email . ')',
                'description' => 'Yönetici oluşturuldu: ' . $user->name,
            ]);
            
            unset($data['yonetici_ad_soyad'], $data['yonetici_email'], $data['yonetici_sifre']);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
