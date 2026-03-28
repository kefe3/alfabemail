# alfabemail
Çocuklar için mail uygulamasıdır.
🚀 ALFABE PORTAL - PROJE ANA DÖKÜMANI
1. VİZYON
Genç nesillerin bireyselleştirilmiş, çok uyaranlı öğrenme yanıtlarını veren; Mailcow üzerine inşa edilmiş bir iletişim ve eğitim yönetimi ekosistemi.

2. KULLANICI ROLLERİ VE FONKSİYONLARI
🟢 YÖNETİCİ (Okul Yönetimi)
Giriş: Mevcut şahsi e-posta adresi ve aktivasyon linkiyle.
Görev: Eğitim sürecinin izlenmesi, rehberlik etme ve sistemin birleştirici rolünü üstlenmesi.
Yetki: Öğretmenleri (branş ve sınıflara dayalı) sistem ekleme.
🔵 ÖĞRETMEN (Sınıf ve Süreç Lideri)
Giriş: Aktivasyon bağlantısı ve şifre oluşturma süreciyle.
Görev: Eğitim planlama, zenginleştirme ve öğrenci rehberliği.
Yetki: * Sınıf açma ve öğrenci listesi (CSV/Excel) yükleniyor.
Öğrencisi için otomatik @alfabe.co maili oluşturma (Mailcow API).
Öğrenci mail adresleri varsayılan olarak ad.soyad uzantıları mümkündür.
Öğretmen, oluşturma aşamasında öğrencinin isteğine göre bu adrese (nick/rumuz) manuel olarak düzenleyebilir.
Mevcut bir öğrencinin kullanıcı adı, veri bütünlüğünü bozmadan öğretmen paneli üzerinden güncellenebilir veya ek takma reklam (takma ad) yapılabilir.
Öğrenci kaydı sırasında veli e-postasını eşleştirme.
Yaka Kartı Üretimi: öğrenciler için karekodlu giriş kartlarını yazdırma.
🟡 VELİ (Akademik Takip)
Giriş: Şahsi mailine gelen link ile.
Görev: Akademik gelişim ve ödev takibi.
Yetki: Öğrencinin posta aktarımının sadece özet analizini görmesi (Kimlere posta atıldı, çalışma performansı vb.).
🔴ÖĞRENCİ (Genç Yetenek)
Giriş: Kamera üzerinden Karekod okutarak.
Özellik: Yaş grubuna özel, görsel uyaranı yüksek ayarları.
Uluslararası Malzeme: Kesikli çizgilerle ayrılır; üstte Karekod, gizli kalacak şifre içeren yaka kartı.
3. TEKNİK İŞ AKIŞI VE PÜF NOKTALARI
API Entegrasyonu: Tüm mail açma işlemleri Mailcow API üzerinden X-API-Keyile yapılır.

Karekod Giriş: Karekod, gizli mail:sifrebilgileri (şifreli şekilde) içerir. Kamera okunduğunda sistem otomatik giriş olur.

Yaka Kartı Tasarımı: - [AD SOYAD]

[KAREKOD (Giriş için)]
----------------------- (Kesme Çizgisi)
[E-POSTA ADRESİ]
[METİN ŞİFRE (Saklamak için)]
4. ANALİZ VE RAPORLAMA
Veli panelinde yaşanan maillerin içeriği okunmaz; Sadece etkinlik analizi (AI destekli çalışma özeti) sunulur.

## İlk Aşama - Kodlanan Modüller

- `src/controllers/auth_controller.js`: Mailcow API bağlantısı için auth controller ve öğrenci oluşturma endpoint handler'ı.
- `src/services/student_mail_service.js`: Öğrenci bilgilerini alıp Mailcow üzerinde otomatik mailbox açma ve güçlü şifre üretimi.
- `src/views/teacher-printable-badge.html` + `src/views/teacher-printable-badge.css`: Öğrenci maili, karekodu ve kesikli şifre alanı içeren yazdırılabilir yaka kartı şablonu.

## Portal Modülleri - Güncel Uygulama Kapsamı

Bu sürümde ana yapı korunarak dört ana panel tek bir portal ekranında birleştirildi:

### 1) Yönetici Paneli
- Toplam öğrenci sayısı
- Toplam öğretmen sayısı
- Öğretmen/sınıf listesi
- Öğretmen panelinden yeni öğrenci kaydedildikçe sayıların dinamik güncellenmesi

### 2) Öğretmen Paneli
- Öğrenci adı, soyadı ve veli maili giriş alanları
- Ad + soyad girildiği anda `ad.soyad@alfabe.co` formatında otomatik kullanıcı adı önerisi
- Öğretmenin kullanıcı adı alanını manuel düzenleyebilmesi (nick/rumuz)
- `Öğrenciyi Kaydet` ile Mailcow API sürecinin simülasyonu
- Simülasyon sonrası otomatik şifre üretimi
- Karekodlu, kesikli çizgili yaka kartı önizlemesi
- Kayıttan sonra aktifleşen `Yaka Kartını Yazdır` butonu

### 3) Öğrenci Giriş Yönetim Modülü
- Son oluşturulan öğrenci hesabının özet görünümü
- Karekodla otomatik giriş akışına hazırlık metni

### 4) Veli Paneli
- Öğrencinin bağlı olduğu veli e-posta bilgisini özetleme
- İçerik yerine etkinlik/akademik takip odaklı panel yaklaşımı

## Teknik Detaylar
- Front-end: HTML + CSS + Vanilla JS
- Tasarım: pastel tonlar, yumuşak köşeler, kart tabanlı düzen
- Çıktı: yazdırmaya uygun yaka kartı önizleme (print CSS)
- Yeni dosyalar:
  - `src/views/portal.html`
  - `src/views/portal.css`
  - `src/views/portal.js`

## Süreç Notu
- Ana mimariye zarar vermeden mevcut modüllere ek geliştirme yapıldı.
- Önceki Mailcow servis/controller dosyaları korunarak, öğretmen odaklı uçtan uca demo akışı portal ekranına taşındı.

## Yeni Başlangıç Ekranı (index.html)
- `index.html` (repo kökü) dinamik karşılama tasarımı eklendi; domain kökünden direkt açılır.
- Kayıp penguen animasyonu kapıdan içeri girer şekilde kurgulandı.
- Penguen tasarımı sadeleştirildi: elinde herhangi bir nesne taşımadan yürür; kapıya yaklaşınca kapı açılır, penguen içeri girer ve kapı üzerinde `alfabe.co` yazısı görünür.
- Giriş kartları rol hiyerarşisine göre sıralandı: `Super Admin → Admin → Bayi → Yönetici → Öğretmen / Veli / Öğrenci`.
- Tasarım pastel renk paleti ve modern kart sistemiyle hazırlandı.

## Rol Hiyerarşisi Sayfaları
- `portal/super_admin.html` eklendi (en üst seviye).
- `portal/admin.html` eklendi (super admin altı yönetim seviyesi).
- `portal/bayi.html` ile aşağı akışa devam eder; ardından `portal/yonetici.html`, `portal/ogretmen.html`, `portal/veli.html`, `portal/ogrenci.html` gelir.

## Demo Giriş Bilgileri (Geçici)
- super_admin: `info@ismailcimen.com.tr / Demo123!`
- admin: `admin@alfabe.co / Demo123!`
- bayi: `bayi@alfabe.co / Demo123!`
- yonetici: `yonetici@alfabe.co / Demo123!`
- ogretmen: `ogretmen@alfabe.co / Demo123!`
- veli: `veli@alfabe.co / Demo123!`
- ogrenci: `ogrenci@alfabe.co / Demo123!`

## Mailcow Proxy Backend (CORS ve Güvenlik için)
- Tarayıcıdan doğrudan Mailcow API çağrısı yapmak yerine `server.js` içinde Express tabanlı bir proxy eklendi.
- Frontend, Mailcow'a doğrudan gitmez; `http://localhost:3000/api/mailcow/*` endpoint'ine istek atar.
- Mailcow host/key bilgileri sunucu tarafında tutulur:
  - `.env` (önerilen kalıcı kullanım)
  - veya `/api/mailcow/config` ile runtime konfigürasyon
- Başlatma:
  1. `cp .env.example .env`
  2. `.env` içinde `MAILCOW_API_BASE_URL` ve `MAILCOW_API_KEY` doldur
  3. `npm install`
  4. `npm start`

## Laravel + Filament Dönüşüm Notu (Fazlı Geçiş)
- `docs/phase-1-sail.md` içinde Faz-1 Docker/Sail hazırlığı dokümante edildi.
- `docker-compose.yml` Mailcow ile aynı external network'e bağlanacak şekilde hazırlandı (`MAILCOW_DOCKER_NETWORK`).
- İlerleyen fazda Laravel 11 + Filament + Spatie roles kurulumu yapılacaktır.

## Öğrenci Sayfası (Tek Dosya)
- `portal/ogrenci.html` eklendi.
- Sol tarafta e-posta/şifre giriş formu, sağ tarafta html5-qrcode ile kamera tabanlı karekod giriş alanı vardır.
- Başarılı giriş sonrası öğrenci mail paneli görünür; gelen kutusu ve mail gönderme (simülasyon) akışı bulunur.
- Öğrenci oturumu `sessionStorage` ile tutulur, sayfa yenilendiğinde tekrar giriş istenmez.

## Öğretmen Paneli - Toplu Öğrenci Yükleme
- `portal/ogretmen.html` içinde drag&drop destekli `.csv/.xlsx` dosya yükleme alanı eklendi.
- Dosya satırları `xlsx.full.min.js` ile okunur; her öğrenci için otomatik `ad.soyad@alfabe.co` önerisi ve 10 karakter karmaşık şifre üretilir.
- Önizleme tablosunda satır bazlı `Düzenle` (nick/mail alanı güncelleme) ve `Sil` aksiyonları bulunur.
- `Hesapları Oluştur` butonu listeleri Mailcow API gönderimine hazır payload formatına dönüştürüp `console.log` çıktısı üretir.

## Veli Sayfası (Dashboard)
- `portal/veli.html` eklendi.
- Üst bilgi alanında Veli ve Öğrenci bilgileri gösterilir.
- İstatistik kartları: haftalık toplam mail, en çok iletişim kurulan kişi, konu filtreli tamamlanan ödev sayısı.
- `Chart.js` ile haftalık mail trafiği bar grafik olarak sunulur.
- Mail içeriğini göstermeyen aktivite timeline özeti içerir.
- Grafik veya analiz düşüşlerinde velinin tek tıkla öğretmene e-posta atabilmesi için `Öğretmenle İletişim` butonu eklendi.

## Yönetici Sayfası (yonetici.html)
- `portal/yonetici.html` eklendi.
- Sidebar sekmeleri: Okul Özeti, Öğretmen Yönetimi, Sistem Ayarları.
- Öğretmen ekleme formu: ad, soyad, şahsi mail, sınıf/branş.
- Öğretmen listesi tablo görünümü ve satır bazlı `Hesabı Askıya Al` + `Şifre Linki Gönder` aksiyonları.
- Veriler `localStorage` içinde `ogretmenler` anahtarında saklanır.

## Bayi Aktivasyon Sayfası (bayi.html)
- `portal/bayi.html` eklendi.
- JavaScript başında sabit Süper Admin tanımı vardır:
  - `const SUPER_ADMIN_EMAIL = "info@ismailcimen.com.tr";`
- İki yüzlü panel yapısı:
  - Süper Admin girişinde `Yeni Bayi Ekle` alanı ve `Tüm Bayileri Listele` tablosu görünür.
  - Bayi girişinde yalnızca kendi iline ait okul ekleme/görüntüleme yetkisi aktif olur.
- Bayi oluşturma akışı: ad, soyad, mevcut mail, sorumlu il/bölge ve okul kotası tanımlanır.
- `Bayi Oluştur` işleminde aktivasyon/panel erişim linki içeren davet maili şablonu üretilir (simülasyon + console payload).
- Okul aktivasyonunda müdüre `Alfabe Portal'a Davetlisiniz` başlıklı tokenlı davet şablonu üretilir.
- Takip tablosunda bayi bazlı okul kayıtları ve aktivasyon durumları (Aktif/Beklemede) izlenir.
- Güvenlik kuralı: Süper Admin e-postasına bağlı kayıtlar silinemez (Sil butonundan muaf).
