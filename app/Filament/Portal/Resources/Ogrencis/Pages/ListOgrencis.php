<?php

namespace App\Filament\Portal\Resources\Ogrencis\Pages;

use App\Filament\Portal\Resources\Ogrencis\OgrenciResource;
use App\Models\Ogrenci;
use App\Models\User;
use App\Services\MailcowService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ListOgrencis extends ListRecords
{
    protected static string $resource = OgrenciResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_student')
                ->label('Yeni Öğrenci Ekle')
                ->url(fn () => static::getResource()::getUrl('create'))
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false),
            Action::make('sync_mailcow')
                ->label('Mailcow\'u Senkronize Et')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                ->requiresConfirmation()
                ->modalHeading('Mailcow Senkronizasyonu')
                ->modalDescription('Mailcow\'da olup sistemde olmayan tüm mailbox\'lar öğrenci olarak içe aktarılır. Sistem mailbox\'ları (admin, info, vb.) atlanır.')
                ->modalSubmitActionLabel('Senkronize Et')
                ->action(function () {
                    try {
                        $mailcow = app(MailcowService::class);
                        if (!$mailcow->isConfigured()) {
                            Notification::make()->title('Mailcow API yapılandırılmamış.')->danger()->send();
                            return;
                        }

                        $mailboxes = $mailcow->listMailboxes();

                        $existingLocalParts = Ogrenci::whereNotNull('mailbox_local_part')
                            ->pluck('mailbox_local_part')
                            ->map(fn ($v) => strtolower($v))
                            ->toArray();

                        $systemLocalParts = [
                            'admin', 'info', 'iletisim', 'noreply', 'postmaster',
                            'ogrenci', 'ogretmen', 'yonetici', 'deneme', 'test',
                            'dmarc', 'spam', 'abuse', 'support', 'mailer-daemon',
                        ];

                        $imported = 0;
                        $errors = 0;

                        foreach ($mailboxes as $mbox) {
                            $localPart = is_array($mbox) ? ($mbox['local_part'] ?? '') : '';
                            if (empty($localPart)) continue;

                            $localPartLower = strtolower($localPart);
                            if (in_array($localPartLower, $existingLocalParts)) continue;
                            if (in_array($localPartLower, $systemLocalParts)) continue;

                            try {
                                $name = $mbox['name'] ?? $localPart;
                                $email = "{$localPart}@" . config('mailcow.domain', 'alfabe.co');
                                $password = Str::random(12);

                                $mailcow->updateMailboxPassword($email, $password);

                                $user = User::create([
                                    'name' => $name,
                                    'email' => $email,
                                    'password' => Hash::make($password),
                                    'is_active' => true,
                                ]);
                                $user->assignRole('ogrenci');

                                $qrToken = Str::random(32);
                                $qrContent = json_encode([
                                    'email' => $email,
                                    'password' => $password,
                                    'token' => $qrToken,
                                ]);
                                $qrSvg = QrCode::size(200)->generate($qrContent);

                                Ogrenci::create([
                                    'user_id' => $user->id,
                                    'mailbox_local_part' => $localPart,
                                    'qr_token' => $qrContent,
                                    'qr_svg' => (string) $qrSvg,
                                ]);

                                $imported++;
                            } catch (\Exception $e) {
                                $errors++;
                            }
                        }

                        if ($imported > 0) {
                            Notification::make()
                                ->title("{$imported} yeni öğrenci içe aktarıldı.")
                                ->body($errors > 0 ? "{$errors} hata oluştu." : null)
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Aktarılacak yeni mailbox bulunamadı.')
                                ->body('Sistem mailbox\'ları (admin, info, vb.) otomatik atlanır.')
                                ->info()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Senkronizasyon hatası')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
