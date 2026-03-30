# ALFABE Mail — Proje Ana Dökümantasyonu

> Çocuklar için güvenli, çok seviyeli eMail ekosistemi. Laravel 13 + Filament v5 + Docker Sail.

---

## 1. VİZYON

Genç nesillerin bireyselleştirilmiş öğrenme yanıtlarını veren; Mailcow API altyapısı üzerine inşa edilmiş iletişim ve eğitim yönetimi ekosistemi.

---

## 2. TEKNOLOJİ YIĞINI

| Katman | Teknoloji |
|---|---|
| Backend | Laravel 13 (PHP 8.5) |
| Admin Panel | Filament v5 |
| Rol/İzin | Spatie Laravel Permission v7 |
| Docker | Laravel Sail (MySQL 8.4 + Redis) |
| QR Kod | SimpleSoftwareIO/simple-qrcode |
| Mailcow Proxy | App\Services\MailcowService |

---

## 3. ROL HİYERARŞİSİ

| Rol | Panel | Yetki |
|---|---|---|
| `super_admin` | `/admin` | Her şey |
| `admin` | `/admin` | Bayiler, okullar, kullanıcılar |
| `bayi` | `/panel` | Kendi okulları, yönetici aktivasyonu |
| `yonetici` | `/panel` | Öğretmen yönetimi |
| `ogretmen` | `/panel` | Öğrenci yönetimi, mailbox oluşturma |
| `veli` | `/veli/dashboard` | Çocuğunun özet raporu |
| `ogrenci` | `/giris` | QR veya form ile giriş |

---

## 4. HIZLI BAŞLANGIÇ (Lokal Geliştirme)

### Gereksinimler
- Docker Desktop
- Git

### Kurulum

```bash
git clone <repo-url> alfabemail
cd alfabemail

# .env dosyasını düzenle (MAILCOW_API_KEY gibi)
cp .env.example .env

# Sail ile başlat
./vendor/bin/sail up -d

# APP_KEY üret (ilk kurulumda)
./vendor/bin/sail artisan key:generate

# Veritabanı ve demo veriler
./vendor/bin/sail artisan migrate --seed
```

### Erişim

| URL | Açıklama |
|---|---|
| `http://localhost:8000` | Ana sayfa (penguen animasyonu) |
| `http://localhost:8000/admin` | Admin/SuperAdmin paneli |
| `http://localhost:8000/panel` | Bayi/Yönetici/Öğretmen paneli |
| `http://localhost:8000/giris` | Öğrenci girişi (QR + form) |

---

## 5. DEMO GİRİŞ BİLGİLERİ

| Rol | E-posta | Şifre |
|---|---|---|
| super_admin | info@ismailcimen.com.tr | Demo123! |
| admin | admin@alfabe.co | Demo123! |
| bayi | bayi@alfabe.co | Demo123! |
| yonetici | yonetici@alfabe.co | Demo123! |
| ogretmen | ogretmen@alfabe.co | Demo123! |
| veli | veli@alfabe.co | Demo123! |
| ogrenci | ogrenci@alfabe.co | Demo123! |

---

## 6. MAILCOW API PROXY

Tüm Mailcow işlemleri `App\Services\MailcowService` üzerinden sunucu tarafında gerçekleşir. CORS hatası tamamen ortadan kalkmıştır.

### Endpoint'ler (auth:sanctum gerektirir)

| Method | URL | İzin | Açıklama |
|---|---|---|---|
| GET | `/api/mailcow/status` | — | Yapılandırma durumu |
| GET | `/api/mailcow/mailboxes` | `kota-sor` | Tüm mailbox listesi |
| GET | `/api/mailcow/quota/{email}` | `kota-sor` | Kota sorgulama |
| POST | `/api/mailcow/mailbox` | `mailbox-olustur` | Öğrenci mailbox'ı oluştur |
| DELETE | `/api/mailcow/mailbox/{email}` | `mailbox-sil` | Mailbox sil |

### .env Mailcow Ayarları

```env
MAILCOW_API_BASE_URL=http://nginx-mailcow:80   # Docker ağı içinde
MAILCOW_API_KEY=your-mailcow-api-key-here
MAILCOW_DOMAIN=alfabe.co
MAILCOW_DEFAULT_QUOTA_MB=2048
```

---

## 7. DOCKER AĞLARI

Laravel Sail hem `sail` hem de `mailcow-network` ağına bağlıdır:

```yaml
# compose.yaml
networks:
  sail:
    driver: bridge
  mailcow-network:
    external: true
    name: mailcowdockerized_mailcow-network
```

---

## 8. VERİTABANI ŞEMASI

```
users ──┬── bayiler ── okullar ── siniflar ── ogrenciler
        ├── veliler ──────────────────────────────╯ (ogrenci_veli pivot)
        └── aktivasyon_tokenleri
```

---

## 9. KLASÖR YAPISI

```
app/
  Http/Controllers/
    MailcowProxyController.php   # Mailcow API proxy endpoint'leri
    OgrenciController.php        # QR giriş
    ActivationController.php     # Token aktivasyonu
  Models/
    User, Bayi, Okul, Sinif, Ogrenci, Veli, AktivasyonToken
  Providers/Filament/
    AdminPanelProvider.php       # /admin — super_admin, admin
    PortalPanelProvider.php      # /panel — bayi, yonetici, ogretmen
  Services/
    MailcowService.php           # Mailcow API servisi
  Filament/
    Resources/                   # Admin panel: Bayi, Okul, User
    Portal/Resources/            # Portal panel: Sinif, Ogrenci
config/
  mailcow.php
_legacy/                         # Eski HTML/JS/Node.js dosyaları (arşiv)
```

---

## 10. PENGUEN ANİMASYONU

`resources/views/welcome.blade.php` içinde:
- Penguen soldan yürür, sol kolunda 3 adet mail taşır
- Kapıya yaklaşırken yavaşlar
- **Son bakış**: `scaleX(-1)` ile geriye döner, bir an durur
- Tekrar yüzünü kapıya çevirir ve içeri girerek kaybolur
- "Pengueni Tekrar Yürüt" butonu animasyonu sıfırlar

---

*Son güncelleme: {{ date('Y-m-d') }} — Laravel 13 + Filament v5 + Docker Sail dönüşümü tamamlandı.*
