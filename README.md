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
Öğrencisi için otomatik @alfabe.comaili oluşturma (Mailcow API).
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
