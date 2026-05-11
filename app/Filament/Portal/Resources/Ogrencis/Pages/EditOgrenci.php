<?php

namespace App\Filament\Portal\Resources\Ogrencis\Pages;

use App\Filament\Portal\Resources\Ogrencis\OgrenciResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EditOgrenci extends EditRecord
{
    protected static string $resource = OgrenciResource::class;

    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return parent::getRecord()->load(['user', 'veliler.user', 'sinif']);
    }

    protected function beforeSave(): void
    {
        $this->getRecord()->load('veliler.user');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ogrenci = $this->getRecord();
        
        if ($ogrenci->user) {
            $nameParts = explode(' ', $ogrenci->user->name, 2);
            $data['first_name'] = $nameParts[0] ?? '';
            $data['last_name'] = $nameParts[1] ?? '';
            $data['user_email'] = $ogrenci->user->email;
            $data['ad_soyad'] = $ogrenci->user->name;
        }
        
        $data['sinif_id'] = $ogrenci->sinif_id;
        $data['veli_email'] = $ogrenci->veliler()->first()?->user?->email;
        $data['anne_email'] = $ogrenci->anne_email;
        $data['baba_email'] = $ogrenci->baba_email;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $ogrenci = $this->getRecord();
        $oldEmail = $ogrenci->user->email ?? '';
        
        $newFirstName = $data['first_name'] ?? '';
        $newLastName = $data['last_name'] ?? '';
        $newNickname = $data['nickname'] ?? null;
        
        if (!empty($newFirstName) && !empty($newLastName)) {
            $mailcow = app(\App\Services\MailcowService::class);
            $slugified = $mailcow->slugify(!empty($newNickname) ? $newNickname : $newFirstName . '.' . $newLastName);
            $newEmail = $slugified . '@alfabe.co';
            
            if (strtolower($newEmail) !== strtolower($oldEmail)) {
                $existingUser = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($newEmail)])
                    ->where('id', '!=', $ogrenci->user_id)
                    ->first();
                if ($existingUser) {
                    Notification::make()
                        ->title('Hata')
                        ->body('Bu e-posta adresi zaten kullanılıyor.')
                        ->danger()
                        ->send();
                    $this->halt();
                }
            }
        }
        
        if (!empty($data['veli_email']) && strtolower($data['veli_email']) === strtolower($newEmail)) {
            Notification::make()
                ->title('Hata')
                ->body('Öğrenci ve veli e-posta adresleri aynı olamaz.')
                ->danger()
                ->send();
            $this->halt();
        }
        
        if (!empty($data['veli_email'])) {
            $existingUser = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($data['veli_email'])])
                ->where('id', '!=', $this->getRecord()->user_id)
                ->first();
            if ($existingUser) {
                $roleName = $existingUser->getRoleNames()->first() ?? 'kullanıcı';
                Notification::make()
                    ->title('Hata')
                    ->body("Bu e-posta zaten {$roleName} olarak kayıtlı.")
                    ->danger()
                    ->send();
                $this->halt();
            }
        }
        
        if (!empty($data['yeni_sifre'])) {
            $this->getRecord()->user->update([
                'password' => Hash::make($data['yeni_sifre'])
            ]);
        }
        
        if (!empty($data['veli_email'])) {
            $veliUser = \App\Models\User::firstOrCreate(
                ['email' => $data['veli_email']],
                ['name' => 'Veli', 'password' => bcrypt(\Illuminate\Support\Str::random(16)), 'is_active' => true]
            );
            $veliUser->assignRole('veli');
            
            $veli = \App\Models\Veli::firstOrCreate(['user_id' => $veliUser->id]);
            $this->getRecord()->veliler()->sync([$veli->id]);
        }
        
        foreach (['anne_email', 'baba_email'] as $emailField) {
            if (!empty($data[$emailField])) {
                $existingUser = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($data[$emailField])])
                    ->where('id', '!=', $this->getRecord()->user_id)
                    ->first();
                if ($existingUser) {
                    $roleName = $existingUser->getRoleNames()->first() ?? 'kullanıcı';
                    $fieldLabel = $emailField === 'anne_email' ? 'Anne' : 'Baba';
                    Notification::make()
                        ->title('Hata')
                        ->body("{$fieldLabel} e-posta zaten {$roleName} olarak kayıtlı.")
                        ->danger()
                        ->send();
                    $this->halt();
                }
            }
        }
        
        unset($data['ad_soyad'], $data['veli_email'], $data['yeni_sifre'], $data['yeni_sifre_tekrar']);
        return $data;
    }
}