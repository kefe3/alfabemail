<?php

namespace App\Console\Commands;

use App\Models\Ogrenci;
use App\Services\MailcowService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FillStudentQr extends Command
{
    protected $signature = 'ogrenci:fill-qr {--force : Mevcut QR kodlarını yeniden oluştur}';
    protected $description = 'QR kodu olmayan öğrenciler için QR kodu oluşturur';

    public function handle(MailcowService $mailcow): void
    {
        $query = Ogrenci::whereNull('qr_token')->orWhereNull('qr_svg');

        if ($this->option('force')) {
            $query = Ogrenci::whereNotNull('id');
            $this->warn('Tüm öğrenciler için QR kodları yeniden oluşturuluyor...');
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('Tüm öğrencilerin QR kodları mevcut.');
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $errors = 0;

        $query->chunk(50, function ($ogrenciler) use ($mailcow, &$updated, &$errors, $bar) {
            foreach ($ogrenciler as $ogrenci) {
                try {
                    if (!$ogrenci->user) {
                        $errors++;
                        $bar->advance();
                        continue;
                    }

                    $email = $ogrenci->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');

                    $existingToken = $ogrenci->qr_token ? json_decode($ogrenci->qr_token, true) : null;

                    if ($existingToken && isset($existingToken['password'])) {
                        $password = $existingToken['password'];
                        $qrToken = $existingToken['token'] ?? Str::random(32);
                    } else {
                        $password = Str::random(12);
                        $qrToken = Str::random(32);

                        try {
                            $mailcow->updateMailboxPassword($email, $password);
                        } catch (\Exception $e) {
                            $this->warn("Mailcow şifre güncelleme hatasi ({$email}): {$e->getMessage()}");
                        }
                    }

                    $qrContent = json_encode([
                        'email' => $email,
                        'password' => $password,
                        'token' => $qrToken,
                    ]);

                    $qrSvg = QrCode::size(400)->generate($qrContent);

                    $ogrenci->qr_token = $qrContent;
                    $ogrenci->qr_svg = (string) $qrSvg;
                    $ogrenci->save();

                    $updated++;
                } catch (\Exception $e) {
                    $this->error("Hata ({$ogrenci->id}): {$e->getMessage()}");
                    $errors++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Tamamlandi: {$updated} güncellendi, {$errors} hata.");
    }
}
