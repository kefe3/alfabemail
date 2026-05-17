<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class MailcowService
{
    private string $apiBaseUrl;
    private string $apiKey;
    private string $mailDomain;
    private int $defaultQuotaMb;

    public function __construct()
    {
        $this->refreshConfig();
    }

    public function refreshConfig(): void
    {
        $this->apiBaseUrl     = rtrim((string) $this->getSetting('mailcow_api_base_url', config('mailcow.api_base_url', '')), '/');
        $this->apiKey         = (string) $this->getSetting('mailcow_api_key', config('mailcow.api_key', ''));
        $this->mailDomain     = strtolower(trim((string) $this->getSetting('mailcow_domain', config('mailcow.domain', 'alfabe.co'))));
        $this->defaultQuotaMb = (int) $this->getSetting('mailcow_default_quota_mb', config('mailcow.default_quota_mb', 100));
    }

    private function getSetting(string $key, mixed $default = null): mixed
    {
        try {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function testConnection(): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $url = "/api/v1/get/domain/{$this->mailDomain}";
            \Log::info("Mailcow test URL: {$this->apiBaseUrl}{$url}");
            $response = $this->http()->get($url);
            \Log::info("Mailcow response: " . $response->status() . " - " . $response->body());
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error("Mailcow connection error: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------
    // Türkçe karakter normalizasyonu (JS slugify ile aynı mantık)
    // -------------------------------------------------------
    public function slugify(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');

        $tr = ['ş'=>'s','Ş'=>'s','ı'=>'i','İ'=>'i','ğ'=>'g','Ğ'=>'g',
                'ü'=>'u','Ü'=>'u','ö'=>'o','Ö'=>'o','ç'=>'c','Ç'=>'c'];
        $value = strtr($value, $tr);

        // NFD normalize & strip diacritics
        $value = \Normalizer::normalize($value, \Normalizer::FORM_D);
        $value = preg_replace('/\p{Mn}/u', '', $value);

        // Boşluk ve izin verilmeyen karakterleri noktaya çevir
        $value = preg_replace('/[^a-z0-9]+/', '.', $value);
        $value = trim($value, '.');
        $value = preg_replace('/\.{2,}/', '.', $value);

        return $value;
    }

    public function createMailboxLocalPart(string $firstName, string $lastName, ?string $nickname = null): string
    {
        $base = !empty($nickname)
            ? trim($nickname)
            : trim($firstName) . '.' . trim($lastName);

        $localPart = $this->slugify($base);

        if (empty($localPart)) {
            throw new RuntimeException('Öğrenci adı/soyadı veya rumuz geçersiz.');
        }

        $email = "{$localPart}@{$this->mailDomain}";
        
        if (\App\Models\User::where('email', $email)->exists()) {
            throw new RuntimeException('Bu e-posta adresi zaten kullanılıyor.');
        }

        return $localPart;
    }

    private function mailboxExists(string $localPart): bool
    {
        try {
            $response = $this->http()->get("/api/v1/get/mailbox/{$localPart}@{$this->mailDomain}", ['timeout' => 3]);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    // -------------------------------------------------------
    // HTTP istemcisi (her istekte yenilenir)
    // -------------------------------------------------------
    private function http(): PendingRequest
    {
        return Http::withHeaders([
            'X-API-Key'    => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->baseUrl($this->apiBaseUrl)->timeout(10)->connectTimeout(5)->withOptions([
            'verify' => false,
        ]);
    }

    // -------------------------------------------------------
    // Mailbox işlemleri
    // -------------------------------------------------------
    public function createStudentMailbox(
        string $firstName,
        string $lastName,
        ?string $nickname = null,
        int $quotaMb = 0,
        ?string $password = null
    ): array {
        $localPart = $this->createMailboxLocalPart($firstName, $lastName, $nickname);
        $email     = "{$localPart}@{$this->mailDomain}";
        $password  = $password ?: $this->generatePassword();
        $quota     = $quotaMb > 0 ? $quotaMb : $this->defaultQuotaMb;

        $response = $this->http()->post('/api/v1/add/mailbox', [
            'local_part' => $localPart,
            'domain'     => $this->mailDomain,
            'name'       => trim("{$firstName} {$lastName}"),
            'password'   => $password,
            'password2'  => $password,
            'active'     => '1',
            'quota'      => $quota,
        ]);

        \Log::info('Mailcow create response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $this->validateMailcowResponse($response, 'Mailbox oluşturulamadı');

        return [
            'email'      => $email,
            'password'   => $password,
            'local_part' => $localPart,
            'full_name'  => trim("{$firstName} {$lastName}"),
        ];
    }

    private function validateMailcowResponse(object $response, string $defaultMsg): void
    {
        if ($response->failed()) {
            $bodyArray = $response->json();
            $msg = is_array($bodyArray) ? ($bodyArray[0]['msg'] ?? $bodyArray[0]['message'] ?? null) : ($bodyArray['msg'] ?? $bodyArray['message'] ?? null);
            
            if ($msg === 'object_exists') {
                throw new RuntimeException('Bu e-posta adresi zaten alınmış. Lütfen farklı bir isim veya rumuz kullanın.');
            }
            
            throw new RuntimeException("{$defaultMsg}: " . ($msg ?? 'Mailcow API hatası'), $response->status());
        }

        $bodyArray = $response->json();
        $body = is_array($bodyArray) && isset($bodyArray[0]) ? $bodyArray[0] : $bodyArray;
        
        if (isset($body['type']) && in_array($body['type'], ['danger', 'warning'], true)) {
            $msg = is_array($body['msg']) ? ($body['msg'][0] ?? $defaultMsg) : ($body['msg'] ?? $body['message'] ?? $defaultMsg);
            
            if ($msg === 'object_exists') {
                throw new RuntimeException('Bu e-posta adresi zaten alınmış. Lütfen farklı bir isim veya rumuz kullanın.');
            }
            
            throw new RuntimeException("{$defaultMsg}: {$msg}");
        }
    }

    public function deleteMailbox(string $email): void
    {
        $response = $this->http()->post('/api/v1/delete/mailbox', [$email]);

        $this->validateMailcowResponse($response, 'Mailbox silinemedi');
    }

    public function updateMailboxPassword(string $email, string $newPassword): array
    {
        $localPart = explode('@', $email)[0];

        $response = $this->http()->post('/api/v1/edit/mailbox', [
            'local_part'  => $localPart,
            'domain'      => $this->mailDomain,
            'password'    => $newPassword,
            'password2'   => $newPassword,
        ]);

        \Log::info('Mailcow password update response', [
            'email'  => $email,
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        $this->validateMailcowResponse($response, 'Şifre güncellenemedi');

        return [
            'email'    => $email,
            'password' => $newPassword,
            'success'  => true,
        ];
    }

    public function getMailboxInfo(string $email): array
    {
        $response = $this->http()->get("/api/v1/get/mailbox/{$email}");

        if ($response->failed()) {
            throw new RuntimeException("Mailbox bilgisi alınamadı: {$email}", $response->status());
        }

        $data = $response->json();
        return [
            'email'          => $email,
            'username'       => $data['username'] ?? $email,
            'name'           => $data['name'] ?? '',
            'quota_used'     => $data['quota_used'] ?? 0,
            'quota'          => $data['quota'] ?? $this->defaultQuotaMb,
            'percent_used'  => $data['percent_in_use'] ?? 0,
            'active'         => $data['active'] ?? true,
            'created'        => $data['created'] ?? null,
        ];
    }

    public function getMailboxQuota(string $email): array
    {
        $response = $this->http()->get("/api/v1/get/mailbox/{$email}");

        if ($response->failed()) {
            throw new RuntimeException("Kota bilgisi alınamadı: {$email}", $response->status());
        }

        $data = $response->json();
        return [
            'email'       => $email,
            'quota_used'  => $data['quota_used']  ?? 0,
            'quota'       => $data['quota']        ?? $this->defaultQuotaMb,
            'percent_used'=> $data['percent_in_use'] ?? 0,
        ];
    }

    public function updateMailboxQuota(string $email, int $quotaMb): array
    {
        $localPart = explode('@', $email)[0];

        $response = $this->http()->post('/api/v1/edit/mailbox', [
            'local_part' => $localPart,
            'domain'     => $this->mailDomain,
            'quota'      => $quotaMb,
        ]);

        \Log::info('Mailcow quota update response', [
            'email'  => $email,
            'quota'  => $quotaMb,
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        $this->validateMailcowResponse($response, 'Kota güncellenemedi');

        return [
            'email'  => $email,
            'quota'  => $quotaMb,
            'success' => true,
        ];
    }

    public function listMailboxes(): array
    {
        $response = $this->http()->get("/api/v1/get/mailbox/all/{$this->mailDomain}");

        if ($response->failed()) {
            throw new RuntimeException('Mailbox listesi alınamadı.', $response->status());
        }

        return $response->json() ?? [];
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiBaseUrl) && !empty($this->apiKey);
    }

    // -------------------------------------------------------
    // Güçlü şifre üretimi (JS generateStudentPassword ile aynı karmaşıklık)
    // -------------------------------------------------------
    private function generatePassword(int $length = 12): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $digits  = '23456789';
        $special = '!@#$%&*';

        $password  = $upper[random_int(0, strlen($upper) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        $all = $upper . $lower . $digits . $special;
        for ($i = 3; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }
}
