<?php

namespace App\Filament\Portal\Resources\Sinifs\Pages;

use App\Filament\Portal\Resources\Sinifs\SinifResource;
use App\Models\Okul;
use App\Services\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateSinif extends CreateRecord
{
    protected static string $resource = SinifResource::class;

    protected function afterCreate(): void
    {
        $ogretmenler = $this->data['ogretmenler'] ?? [];
        if (!empty($ogretmenler) && is_array($ogretmenler)) {
            $this->record->ogretmenler()->sync($ogretmenler);
        }
        
        ActivityLogger::created($this->record, 'Sınıf oluşturuldu: ' . $this->record->ad);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['okul_id']) || empty($data['okul_id'])) {
            if (auth()->user()?->hasRole('yonetici')) {
                $okul = Okul::where('yonetici_user_id', auth()->id())->first();
                $data['okul_id'] = $okul?->id;
                
                if (!$data['okul_id']) {
                    Notification::make()
                        ->title('Hata')
                        ->body('Bu yöneticiye ait okul bulunamadı.')
                        ->danger()
                        ->send();
                    $this->halt();
                }
            }
        }

        return $data;
    }

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
}