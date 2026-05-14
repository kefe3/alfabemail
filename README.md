# Alfabe Mail — Çocuklar için Güvenli E-posta Sistemi

> **v1.0** — Kapsül Serix Teknoloji Platformu

Çocukların güvenli, reklamsız ve kontrollü bir ortamda e-posta kullanmasını sağlayan eğitim odaklı mail platformu.

---

## 🚀 Özellikler

### Paneller
| Panel | URL | Kullanıcı | Açıklama |
|-------|-----|-----------|----------|
| Admin | `/admin` | ...@alfabe.co | Tüm yönetim |
| Portal | `/panel` | ogretmen/yonetici/veli | Öğretmen, yönetici ve veli paneli |
| Öğrenci | `/giris` | ...@alfabe.co | Karekodla giriş, mail kullanımı |

---

## ✅ v1.0 Tamamlanan Özellikler

### Giriş & Kimlik Doğrulama
- [x] Admin/Portal panel girişi (Filament auth)
- [x] Öğrenci karekod ile giriş
- [x] Öğrenci normal giriş
- [x] Aktivasyon linki ile ilk giriş
- [x] Kayıt sistemi (e-posta doğrulama → şifre belirleme → admin onayı)
- [x] Öğrenci oluştururken okul seçimi + okula göre sınıf seçimi

### Öğrenci Mail Sistemi
- [x] Gelen kutusu (IMAP)
- [x] Giden kutusu
- [x] Mail gönderme (SMTP)
- [x] Mail detay modalı
- [x] UTF-8 Türkçe destek
- [x] Çocuk dostu UI (penguen maskot 🐧)
- [x] Yaka kartı oluşturma (karekodlu)

### Öğretmen Paneli
- [x] Sınıf yönetimi (CRUD)
- [x] Öğrenci yönetimi (CSV yükleme, Mailcow mailbox oluşturma)
- [x] Veli e-posta eşleştirme
- [x] Yaka kartı toplu yazdırma
- [x] Pivot tabanlı sınıf filtreleme

### Veli Paneli (6 özellik)
- [x] AI Haftalık Özet Raporu (VeliAnalizService)
- [x] Aktivite Takvimi (son 7 gün)
- [x] Kota/Uyarı Bildirimleri (Mailcow API)
- [x] Veli-Öğretmen Mesajlaşma
- [x] Öğrenci Şifre Sıfırlama
- [x] Çoklu Öğrenci Karşılaştırma

### Admin Paneli
- [x] Kullanıcı yönetimi (CRUD, rol atama)
- [x] Okul yönetimi ve onay sistemi
- [x] Sponsor yönetimi
- [x] Aktivite logları
- [x] Hata Bildirisi yönetimi
- [x] Yeni Kullanıcı Onay Sistemi (kayıt → onay → kullanıcıya taşıma)
- [x] Yetki/rol yönetimi
- [x] Öğretmen oluştururken okul seçimi (okul yoksa yeni okul oluşturma)
- [x] Öğretmen oluştururken sınıf seçimi (sınıf yoksa yeni sınıf oluşturma)
- [x] Veli oluştururken öğrenci seçimi (hangi öğrencinin velisi olduğu)

### Hata Bildir Sistemi
- [x] Tüm sayfalarda floating ⚠️ butonu
- [x] AJAX ile form gönderimi
- [x] Ekran görüntüsü yükleme
- [x] Admin panelinde yönetim (çözüldü/çözülmedi)

### E-posta Güvenliği (DNS)
- [x] SPF kaydı (`v=spf1 mx a:mail.alfabe.co -all`)
- [x] DKIM imzası (RSA 2048 bit)
- [x] DMARC politikası (`p=quarantine`)
- [x] PTR kaydı (`45.94.4.39 → mail.alfabe.co`)

---

## 📡 API Endpoint'leri

### Öğrenci
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/ogrenci/login` | Giriş |
| POST | `/ogrenci/qr-login` | Karekod girişi |
| GET | `/ogrenci/inbox` | Gelen kutusu |
| GET | `/ogrenci/sent` | Giden kutusu |
| POST | `/ogrenci/send-mail` | Mail gönderme |
| POST | `/ogrenci/log-read` | Okundu kaydı |
| GET | `/ogrenci/yaka-karti/{id}` | Yaka kartı |

### Kayıt
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/kayit/send-code` | Doğrulama kodu gönder |
| POST | `/kayit/verify-code` | Kodu doğrula |
| POST | `/kayit/complete` | Kaydı tamamla |

### Veli
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/veli/mesaj-gonder` | Öğretmene mesaj |
| POST | `/veli/sifre-sifirla` | Öğrenci şifre sıfırlama |

### Hata Bildir
| Method | Route | Açıklama |
|--------|-------|----------|
| POST | `/hata-bildir` | Hata bildirisi gönder |

---

## 🛠 Teknik Mimari

### Stack
- **Backend**: Laravel 13 + Filament 4
- **Veritabanı**: MySQL (Docker)
- **Mail Sunucusu**: Mailcow (Docker)
- **Cache/Queue**: Redis
- **Yetki**: Spatie Laravel Permission
- **Frontend**: Blade + Chart.js + IMAP/SMTP

### Veritabanı Hiyerarşisi
```
okullar → siniflar → ogrenciler → ogrenci_veli (pivot)
       → users → (roles: admin, yonetici, ogretmen, veli, ogrenci)
       → veliler
       → pending_users (kayıt onay bekleme)
```

### Roller
1. **admin** — Tüm yönetim
2. **yonetici** — Okul yönetimi, öğretmen/sınıf yönetimi
3. **ogretmen** — Öğrenci yönetimi
4. **veli** — Akademik takip, AI raporları
5. **ogrenci** — Mail kullanımı

---

## 🌐 Sunucu Bilgileri

### Mailcow
| Servis | Adres |
|--------|-------|
| API | `https://mail.alfabe.co/api/v1/` |
| SMTP | `mail.alfabe.co:587` (TLS) |
| IMAP | `mail.alfabe.co:993` (SSL) |
| Domain | `alfabe.co` |

### DNS Kayıtları
| Kayıt | Değer | Durum |
|-------|-------|-------|
| SPF | `v=spf1 mx a:mail.alfabe.co -all` | ✅ |
| DKIM | `dkim._domainkey.alfabe.co` | ✅ |
| DMARC | `v=DMARC1; p=quarantine; rua=mailto:postmaster@alfabe.co` | ✅ |
| PTR | `45.94.4.39 → mail.alfabe.co` | ✅ |

### Development
- Local: `http://127.0.0.1:8001`
- Docker: `http://localhost:8000`

---

## 📁 Önemli Dosyalar

### Servisler
- `app/Services/MailcowService.php` — Mailcow API
- `app/Services/VeliAnalizService.php` — AI haftalık özet
- `app/Services/ActivityLogger.php` — Aktivite loglama
- `app/Services/PermissionService.php` — İzin yönetimi
- `app/Services/DynamicMailer.php` — Dinamik mail gönderimi

### Controller
- `app/Http/Controllers/OgrenciController.php` — Öğrenci işlemleri
- `app/Http/Controllers/VeliController.php` — Veli işlemleri
- `app/Http/Controllers/HataBildirController.php` — Hata bildirimi
- `app/Http/Controllers/KayitController.php` — Kayıt işlemleri

### Modeller
- `app/Models/User.php` (roller, `okul()`/`bagli_okul()`/ogrenci/veli/ogretmen ilişkileri)
- `app/Models/Ogrenci.php`, `Veli.php`, `Sinif.php`, `Okul.php`
- `app/Models/PendingUser.php`, `HataBildirisi.php`, `VeliMesaj.php`
- `app/Models/ActivityLog.php`, `MailAktiviteLog.php`, `Sponsor.php`

### Filament Kaynakları (Admin)
- `app/Filament/Resources/PendingUserResource.php` — Yeni Kullanıcı Onayı
- `app/Filament/Resources/HataBildirisis/` — Hata Bildirisi Yönetimi
- `app/Filament/Resources/Users/` — Kullanıcı Yönetimi
- `app/Filament/Resources/ActivityLogs/` — Aktivite Logları
- `app/Filament/Resources/Sponsors/` — Sponsor Yönetimi
- `app/Filament/Resources/Okuls/Pages/OkulOnay/` — Okul Onayları

### Görünümler
- `resources/views/welcome.blade.php` — Anasayfa
- `resources/views/ogrenci/` — Öğrenci dashboard, yaka kartı
- `resources/views/filament/portal/widgets/veli-dashboard.blade.php` — Veli dashboard
- `resources/views/partials/hata-bildir.blade.php` — Hata bildir modalı
- `resources/views/partials/kayit.blade.php` — Kayıt modalı
- `resources/views/emails/verification-code.blade.php` — Doğrulama e-postası

---

## 🔧 Geliştirme Notları

### Port Kullanımı
- Local: `8001` (`.env`'de `APP_URL=http://localhost:8000` olsa da localde 8001)
- Docker internal: `8000`
- Storage symlink: `public/storage → /var/www/html/storage/app/public` (host mutlak yol değil)

### Koyu Tema Uyumluluğu
Filament widget view'larında Tailwind kullanılmaz (purge sorunu). Tüm stiller inline olarak yazılır. Input alanlarında `color:#1a202c` ile koyu temada okunabilirlik sağlanır.

### Yeni Resource Eklerken
```php
use BackedEnum;

protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
protected static ?string $model = MyModel::class;
protected static bool $shouldRegisterNavigation = true;

public static function canAccess(): bool
{
    return auth()->user()?->hasRole('admin') ?? false;
}

public static function getPages(): array
{
    return ['index' => Pages\ListMyModel::route('/')];
}
```

---

## 🚧 Gelecek Planları

- [ ] IMAP inbox okuma iyileştirmesi
- [ ] Öğrenci mail paneli UI redesign
- [ ] Ek dosya ekleme (resim, belge)
- [ ] Arama ve filtreleme
- [ ] Gamification rozetleri
- [ ] Mobil uyum

---

*Kapsül Serix Teknoloji Platformu Destek Ofisi — Konya*
