<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Ogrenci;
use App\Models\Veli;
use App\Services\MailcowService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

#[Signature('quota:check-notify')]
#[Description('Öğrenci mail kotasını kontrol eder ve velileri bilgilendirir')]
class CheckQuotaAndNotify extends Command
{
    protected $signature = 'quota:check-notify';
    protected $description = 'Öğrenci mail kotasını kontrol eder ve velileri bilgilendirir';

    public function handle()
    {
        $this->info('Kota kontrolü başlatılıyor...');

        $ogrenciler = Ogrenci::whereHas('veliler')->with(['user', 'veliler'])->get();
        $mailService = new MailcowService();
        $bildirildi = 0;
        $atlandi = 0;

        foreach ($ogrenciler as $ogrenci) {
            if (!$ogrenci->user || !$ogrenci->mailbox_local_part) {
                $atlandi++;
                continue;
            }

            try {
                $email = $ogrenci->mailbox_local_part . '@' . config('mailcow.domain');
                $quotaInfo = $mailService->getMailboxQuota($email);
                $percentUsed = $quotaInfo['percent_used'] ?? 0;

                if ($percentUsed >= 80) {
                    foreach ($ogrenci->veliler as $veli) {
                        if ($veli->user && $veli->user->email) {
                            $this->notifyParent($veli->user->email, $ogrenci, $percentUsed, $quotaInfo['quota_used'], $quotaInfo['quota']);
                            $bildirildi++;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Kota kontrol hatası - {$ogrenci->id}: " . $e->getMessage());
                $atlandi++;
            }
        }

        $this->info("İşlem tamamlandı. Bildirilen: {$bildirildi}, Atlanan: {$atlandi}");
        return Command::SUCCESS;
    }

    private function notifyParent(string $veliEmail, Ogrenci $ogrenci, int $percent, int $used, int $quota): void
    {
        $ogrenciAd = $ogrenci->user->name ?? 'Öğrenci';
        $usedMb = number_format($used / 1024, 2);
        $quotaMb = number_format($quota / 1024, 2);

        $subject = "📧 {$ogrenciAd} - Mail Kotası Uyarısı";
        $body = "
        <h2>Mail Kotası Uyarısı</h2>
        <p>Sayın Velimiz,</p>
        <p><strong>{$ogrenciAd}</strong> adlı öğrencinin mail kotası <strong>%{$percent}</strong> seviyesine ulaşmıştır.</p>
        <ul>
            <li>Kullanılan: {$usedMb} MB</li>
            <li>Toplam Kota: {$quotaMb} MB</li>
        </ul>
        <p>Lütfen gerekirse okul yönetimi ile iletişime geçin.</p>
        <p>İyi günler,<br>AlfabeMail Sistem</p>
        ";

        try {
            Mail::html($body, function ($message) use ($veliEmail, $subject) {
                $message->to($veliEmail)
                    ->subject($subject);
            });
            $this->line("Bildirim gönderildi: {$veliEmail}");
        } catch (\Exception $e) {
            Log::error("Veli bildirim hatası - {$veliEmail}: " . $e->getMessage());
        }
    }
}