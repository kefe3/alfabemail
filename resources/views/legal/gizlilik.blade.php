<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="/favicon.png" />
  <title>Gizlilik Politikası | ALFABE Mail</title>
  <link rel="stylesheet" href="/css/portal.css" />
  <style>
    .legal-page { max-width: 820px; margin: 40px auto; padding: 0 20px 60px; }
    .legal-page h1 { font-size: clamp(24px,4vw,36px); margin-bottom: 8px; color: var(--ink); }
    .legal-page .legal-updated { color: var(--muted); font-size: 14px; margin-bottom: 28px; }
    .legal-page h2 { font-size: 20px; margin: 28px 0 10px; color: var(--ink); }
    .legal-page p, .legal-page li { color: #3a5068; line-height: 1.7; font-size: 15px; }
    .legal-page ul { padding-left: 22px; margin: 8px 0 14px; }
    .legal-page a { color: var(--primary); }
    .legal-back { display: inline-block; margin-bottom: 18px; color: var(--primary); font-weight: 700; text-decoration: none; }
    .legal-back:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <main class="legal-page">
    <a class="legal-back" href="{{ url('/') }}">&larr; Ana Sayfaya Dön</a>

    <h1>Gizlilik Politikası</h1>
    <p class="legal-updated">Son güncelleme: {{ date('d.m.Y') }}</p>

    <p>
      Alfabe Mail olarak gizliliğinize verdiğimiz önem doğrultusunda, bu politika
      kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklamaktadır.
    </p>

    <h2>1. Topladığımız Bilgiler</h2>
    <ul>
      <li><strong>Hesap Bilgileri:</strong> Ad, soyad, e-posta adresi, şifre (şifrelenmiş olarak saklanır), okul ve sınıf bilgisi</li>
      <li><strong>Profil Bilgileri:</strong> Öğrenci numarası, yaka kartı bilgileri, veli ilişkilendirme verileri</li>
      <li><strong>Kullanım Verileri:</strong> Giriş zamanları, oturum süresi, kullanılan özellikler, e-posta gönderim-alım istatistikleri</li>
      <li><strong>Cihaz Bilgileri:</strong> IP adresi, tarayıcı türü, işletim sistemi, ekran çözünürlüğü</li>
      <li><strong>QR Kod Verileri:</strong> Karekod tarama zamanı ve doğrulama kayıtları</li>
    </ul>

    <h2>2. Bilgileri Kullanım Amaçlarımız</h2>
    <ul>
      <li>Hizmetlerimizi sunmak ve geliştirmek</li>
      <li>Öğrenci, veli, öğretmen ve yönetici hesaplarını yönetmek</li>
      <li>Güvenli e-posta iletişimini sağlamak</li>
      <li>Veli raporları ve gelişim özetlerini hazırlamak</li>
      <li>Platform güvenliğini sağlamak ve kötüye kullanımı önlemek</li>
      <li>Yasal düzenlemelere uymak</li>
    </ul>

    <h2>3. Bilgi Paylaşımı</h2>
    <p>
      Kişisel bilgilerinizi aşağıdaki durumlar dışında üçüncü taraflarla paylaşmıyoruz:
    </p>
    <ul>
      <li>Yasal zorunluluklar gereği</li>
      <li>İlgili okul yönetimi ile (sadece sözleşmeli okullar kapsamında)</li>
      <li>Hizmet sunumu için gerekli olan hizmet sağlayıcılarla (veri barındırma, vb.) — bu taraflar da veri güvenliği yükümlülüğü altındadır</li>
    </ul>

    <h2>4. Veri Güvenliği</h2>
    <p>
      Verilerinizin güvenliği için aşağıdaki tedbirleri uyguluyoruz:
    </p>
    <ul>
      <li>Şifreler tek yönlü hash algoritması ile saklanır</li>
      <li>SSL/TLS şifrelemesi ile veri iletimi korunur</li>
      <li>Erişim kontrolleri ve yetkilendirme mekanizmaları uygulanır</li>
      <li>Düzenli güvenlik denetimleri yapılır</li>
      <li>Çocukların verileri için ek koruma önlemleri alınır</li>
    </ul>

    <h2>5. Çocukların Gizliliği</h2>
    <p>
      Alfabe Mail, çocuklara yönelik bir platform olarak özellikle dikkatli davranır:
    </p>
    <ul>
      <li>18 yaş altı kullanıcıların verileri yalnızca veli/okul onayıyla işlenir</li>
      <li>Çocuklara yönelik reklam ve pazarlama yapılmaz</li>
      <li>Öğrenci verileri yalnızca eğitim amaçlarıyla kullanılır</li>
      <li>QR kod girişleri zamanlı ve kısıtlı oturumlarla korunur</li>
    </ul>

    <h2>6. Veri Saklama Süresi</h2>
    <p>
      Kişisel verileriniz, işlenme amacının gerektirdiği süre boyunca ve yasal
      yükümlülükler çerçevesinde saklanır. Hesabınızın silinmesi talebinde,
      yasal saklama yükümlülükleri hariç tüm verileriniz kalıcı olarak silinir.
    </p>

    <h2>7. Haklarınız</h2>
    <p>
      Detaylı bilgi için <a href="{{ route('kvkk') }}">KVKK Aydınlatma Metni</a>'ni inceleyebilirsiniz.
      Sorularınız için <strong>iletisim@alfabe.co</strong> adresine ulaşabilirsiniz.
    </p>
  </main>

  <footer class="footer">
    <div class="footer-wrap">
      <section>
        <h3>ALFABE</h3>
        <p>Çocuklar için güvenli eMail dünyası.</p>
      </section>
      <section>
        <h3>Yasal</h3>
        <ul>
          <li><a href="{{ route('kvkk') }}">KVKK</a></li>
          <li><a href="{{ route('gizlilik') }}">Gizlilik Politikası</a></li>
          <li><a href="{{ route('kullanim-sartlari') }}">Kullanım Şartları</a></li>
          <li><a href="{{ route('cerez-politikasi') }}">Çerez Politikası</a></li>
        </ul>
      </section>
    </div>
  </footer>

</body>
</html>
