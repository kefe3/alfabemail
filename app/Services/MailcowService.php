<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MailcowService
{
    private string $apiBaseUrl;
    private string $apiKey;
    private string $mailDomain;
    private int $defaultQuotaMb;

    public function __construct()
    {
        $this->apiBaseUrl   = rtrim((string) config('mailcow.api_base_url', ''), '/');
        $this->apiKey       = (string) config('mailcow.api_key', '');
        $this->mailDomain   = strtolower(trim((string) config('mailcow.domain', 'alfabe.co')));
        $this->defaultQuotaMb = (int) config('mailcow.default_quota_mb', 2048);

        if (empty($this->apiBaseUrl) || empty($this->apiKey)) {
            throw new RuntimeException('Mailcow yapılandırması eksik: MAILCOW_API_BASE_URL ve MAILCOW_API_KEY tanımlı olmalı.');
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

        return $localPart;
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
        ])->baseUrl($this->apiBaseUrl)->timeout(15);
    }

    // -------------------------------------------------------
    // Mailbox işlemleri
    // -------------------------------------------------------
    public function createStudentMailbox(
        string $firstName,
        string $lastName,
        ?string $nickname = null,
        int $quotaMb = 0
    ): array {
        $localPart = $this->createMailboxLocalPart($firstName, $lastName, $nickname);
        $email     = "{$localPart}@{$this->mailDomain}";
        $password  = $this->generatePassword();
        $quota     = $quotaMb > 0 ? $quotaMb : $this->defaultQuotaMb;

        $response = $this->http()->post('/api/v1/add/mailbox', [[
            'local_part' => $localPart,
            'domain'     => $this->mailDomain,
            'name'       => trim("{$firstName} {$lastName}"),
            'quota'      => $quota,
            'password'   => $password,
            'password2'  => $password,
            'active'     => '1',
        ]]);

        if ($response->failed()) {
            $msg = $response->json('msg') ?? $response->json('message') ?? 'Mailcow API hatası';
            throw new RuntimeException("Mailbox oluşturulamadı: {$msg}", $response->status());
        }

        return [
            'email'      => $email,
            'password'   => $password,
            'local_part' => $localPart,
            'full_name'  => trim("{$firstName} {$lastName}"),
        ];
    }

    public function deleteMailbox(string $email): void
    {
        $response = $this->http()->post('/api/v1/delete/mailbox', [$email]);

        if ($response->failed()) {
            $msg = $response->json('msg') ?? 'Mailcow API hatası';
            throw new RuntimeException("Mailbox silinemedi: {$msg}", $response->status());
        }
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
