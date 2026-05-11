<?php

namespace App\Filament\Portal\Resources\Okuls\Pages;

use App\Filament\Portal\Resources\Okuls\OkulResource;
use App\Models\Okul;
use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateOkul extends CreateRecord
{
    protected static string $resource = OkulResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Oluştur')
                ->submit('create'),
            Action::make('cancel')
                ->label('İptal')
                ->url($this->previousUrl ?? static::getUrl())
                ->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $yoneticiTipi = $data['yonetici_tipi'] ?? 'yeni';
        
        unset($data['yonetici_tipi'], $data['yonetici_sifre_tekrar']);

        if ($yoneticiTipi === 'yeni' && !empty($data['yonetici_ad_soyad']) && !empty($data['yonetici_email'])) {
            $user = User::create([
                'name' => $data['yonetici_ad_soyad'],
                'email' => $data['yonetici_email'],
                'password' => bcrypt($data['yonetici_sifre']),
                'is_active' => true,
            ]);
            $user->assignRole('yonetici');
            $data['yonetici_user_id'] = $user->id;
            
            ActivityLogger::created($user, 'Yeni yönetici oluşturuldu: ' . $data['yonetici_ad_soyad']);
            
            unset($data['yonetici_ad_soyad'], $data['yonetici_email'], $data['yonetici_sifre'], $data['yonetici_sifre_tekrar']);
        } elseif ($yoneticiTipi === 'mevcut' && !empty($data['yonetici_user_id'])) {
            $user = User::find($data['yonetici_user_id']);
            if ($user) {
                $user->assignRole('yonetici');
            }
        }

        if (auth()->user()?->hasRole('bayi')) {
            $data['bayi_id'] = auth()->user()->bayi?->id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $bayi = auth()->user()?->hasRole('bayi') ? auth()->user()?->bayi : null;
        
        ActivityLogger::created($this->record, 
            'Okul oluşturuldu: ' . $this->record->ad . 
            ($bayi ? ' - Bayi: ' . $bayi->id : '')
        );
    }
}