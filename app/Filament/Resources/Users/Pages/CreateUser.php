<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Ogrenci;
use App\Models\Okul;
use App\Models\User;
use App\Models\Veli;
use App\Services\ActivityLogger;
use App\Services\MailcowService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?array $ogrenciData = null;
    private ?array $ogretmenData = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $ogrenciRoleId = Role::where('name', 'ogrenci')->value('id');
        if ($ogrenciRoleId && in_array($ogrenciRoleId, $data['roles'] ?? [])) {
            $this->ogrenciData = [
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'nickname' => $data['nickname'] ?? null,
                'sinif_id' => $data['sinif_id'] ?? null,
                'anne_email' => $data['anne_email'] ?? null,
                'baba_email' => $data['baba_email'] ?? null,
            ];

            $data['name'] = trim($this->ogrenciData['first_name'] . ' ' . $this->ogrenciData['last_name']);
            $data['email'] = null;

            unset($data['first_name'], $data['last_name'], $data['nickname'], $data['sinif_id'], $data['anne_email'], $data['baba_email']);
        }

        $ogretmenRoleId = Role::where('name', 'ogretmen')->value('id');
        if ($ogretmenRoleId && in_array($ogretmenRoleId, $data['roles'] ?? [])) {
            $this->ogretmenData = [
                'sinif_ids' => $data['sinif_ids'] ?? [],
            ];
            unset($data['sinif_ids']);
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($this->ogrenciData === null) {
            return parent::handleRecordCreation($data);
        }

        $od = $this->ogrenciData;
        $sifre = $data['password'] ?? null;

        $mailcow = app(MailcowService::class);

        if (!$mailcow->isConfigured()) {
            Notification::make()
                ->title('Hata')
                ->body('Mailcow API yapılandırılmamış.')
                ->danger()
                ->send();
            $this->halt();
        }

        if (!$mailcow->testConnection()) {
            Notification::make()
                ->title('Hata')
                ->body('Mailcow sunucusuna bağlanılamıyor.')
                ->danger()
                ->send();
            $this->halt();
        }

        try {
            $mailbox = $mailcow->createStudentMailbox($od['first_name'], $od['last_name'], $od['nickname'], 0, $sifre);
        } catch (\Exception $e) {
            Notification::make()
                ->title('E-posta Kutusu Oluşturma Hatası')
                ->body($e->getMessage())
                ->danger()
                ->send();
            $this->halt();
        }

        $ogrenciEmail = "{$mailbox['local_part']}@" . config('mailcow.domain', 'alfabe.co');

        return DB::transaction(function () use ($od, $data, $mailbox, $ogrenciEmail, $sifre) {
            $user = User::create([
                'name' => trim($od['first_name'] . ' ' . $od['last_name']),
                'email' => $ogrenciEmail,
                'password' => Hash::make($sifre),
                'is_active' => $data['is_active'] ?? true,
            ]);
            $user->assignRole('ogrenci');

            $veliIds = [];
            $veliEmails = array_filter([$od['anne_email'], $od['baba_email']]);

            foreach ($veliEmails as $veliEmail) {
                $veliUser = User::firstOrCreate(
                    ['email' => $veliEmail],
                    ['name' => 'Veli', 'password' => bcrypt('Veli123!'), 'is_active' => true]
                );
                $veliUser->assignRole('veli');

                $veli = Veli::firstOrCreate(['user_id' => $veliUser->id]);
                $veliIds[] = $veli->id;
            }

            $qrToken = Str::random(32);
            $qrContent = json_encode([
                'email' => $user->email,
                'password' => $sifre,
                'token' => $qrToken,
            ]);
            $qrSvg = QrCode::size(200)->generate($qrContent);

            $ogrenci = Ogrenci::create([
                'user_id' => $user->id,
                'sinif_id' => $od['sinif_id'],
                'mailbox_local_part' => $mailbox['local_part'],
                'qr_token' => $qrContent,
                'qr_svg' => (string) $qrSvg,
                'anne_email' => $od['anne_email'],
                'baba_email' => $od['baba_email'],
            ]);

            if (!empty($veliIds)) {
                $ogrenci->veliler()->attach($veliIds);
            }

            Notification::make()
                ->title('Başarılı')
                ->body("Öğrenci {$user->name} başarıyla oluşturuldu. E-posta: {$ogrenciEmail}")
                ->success()
                ->send();

            ActivityLogger::created($user, 'Öğrenci oluşturuldu (Admin): ' . $user->name . ' - E-posta: ' . $ogrenciEmail);

            return $user->load('ogrenci');
        });
    }

    protected function afterCreate(): void
    {
        if ($this->ogrenciData !== null) {
            return;
        }

        $yoneticiRoleId = Role::where('name', 'yonetici')->value('id');
        if ($yoneticiRoleId && $this->record->hasRole('yonetici')) {
            $okulId = $this->form->getState()['okul_id'] ?? null;
            if ($okulId) {
                Okul::where('id', $okulId)->update(['yonetici_user_id' => $this->record->id]);
            }
        }

        if ($this->ogretmenData !== null) {
            if (!empty($this->ogretmenData['sinif_ids'])) {
                $this->record->ogretmen_sinifler_pivot()->sync($this->ogretmenData['sinif_ids']);
            }
        }

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
