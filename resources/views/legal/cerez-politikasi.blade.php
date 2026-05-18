<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="/favicon.png" />
  <title>Çerez Politikası | ALFABE Mail</title>
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
    .cookie-table { width: 100%; border-collapse: collapse; margin: 14px 0 20px; font-size: 14px; }
    .cookie-table th, .cookie-table td { padding: 10px 12px; border: 1px solid #d8e2f0; text-align: left; }
    .cookie-table th { background: #eef4ff; font-weight: 700; color: var(--ink); }
    .cookie-table td { color: #3a5068; }
  </style>
</head>
<body>

  <main class="legal-page">
    <a class="legal-back" href="{{ url('/') }}">&larr; Ana Sayfaya Dön</a>

    <h1>Çerez Politikası</h1>
    <p class="legal-updated">Son güncelleme: {{ date('d.m.Y') }}</p>

    <p>
      Alfabe Mail, web sitesi ve platformunu kullanımınızı iyileştirmek için çerezler ve
      benzeri teknolojiler kullanmaktadır. Bu politika, hangi çerezleri kullandığımızı ve
      bunları nasıl yönetebileceğinizi açıklamaktadır.
    </p>

    <h2>1. Çerez Nedir?</h2>
    <p>
      Çerezler, web sitesinin tarayıcınıza yerleştirdiği küçük metin dosyalarıdır. Siteyi
      tekrar ziyaret ettiğinizde bu dosyalar okunarak size daha iyi bir deneyim sunulur.
    </p>

    <h2>2. Kullandığımız Çerez Türleri</h2>

    <table class="cookie-table">
      <thead>
        <tr>
          <th>Çerez Türü</th>
          <th>Açıklama</th>
          <th>Zorunlu mu?</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Oturum Çerezleri</strong></td>
          <td>Giriş durumunuzu ve oturum bilginizi saklar. Tarayıcı kapatıldığında silinir.</td>
          <td>Evet</td>
        </tr>
        <tr>
          <td><strong>Güvenlik Çerezleri</strong></td>
          <td>CSRF koruması ve kimlik doğrulama için kullanılır.</td>
          <td>Evet</td>
        </tr>
        <tr>
          <td><strong>QR Token Çerezleri</strong></td>
          <td>Karekod ile giriş sırasında doğrulama verisini geçici olarak saklar.</td>
          <td>Evet</td>
        </tr>
        <tr>
          <td><strong>Tercih Çerezleri</strong></td>
          <td>Dil seçimi ve tema tercihleri gibi ayarlarınızı hatırlar.</td>
          <td>Hayır</td>
        </tr>
        <tr>
          <td><strong>Analitik Çerezleri</strong></td>
          <td>Platform kullanım istatistiklerini anonim olarak toplar.</td>
          <td>Hayır</td>
        </tr>
      </tbody>
    </table>

    <h2>3. Zorunlu Çerezler</h2>
    <p>
      Aşağıdaki çerezler platformun çalışması için kesinlikle gereklidir ve devre dışı bırakılamaz:
    </p>
    <ul>
      <li><strong>alfabe_session:</strong> Oturum kimliğinizi saklar (tarayıcı kapatılana kadar)</li>
      <li><strong>XSRF-TOKEN:</strong> Siteler arası istek sahteciliğine karşı koruma sağlar</li>
      <li><strong>remember_web:</strong> &ldquo;Beni hatırla&rdquo; seçeneği ile giriş bilgilerinizi korur (30 gün)</li>
    </ul>

    <h2>4. İsteğe Bağlı Çerezler</h2>
    <p>
      Analitik ve tercih çerezleri, platform deneyiminizi iyileştirmek için kullanılır.
      Bu çerezleri tarayıcı ayarlarınızdan devre dışı bırakabilirsiniz.
    </p>

    <h2>5. Çerezleri Yönetme</h2>
    <p>
      Çerezleri tarayıcınızın ayarları üzerinden yönetebilirsiniz:
    </p>
    <ul>
      <li><strong>Chrome:</strong> Ayarlar → Gizlilik ve güvenlik → Çerezler</li>
      <li><strong>Firefox:</strong> Ayarlar → Gizlilik ve Güvenlik → Çerezler</li>
      <li><strong>Safari:</strong> Tercihler → Gizlilik → Çerezler</li>
      <li><strong>Edge:</strong> Ayarlar → Çerezler ve site izinleri</li>
    </ul>
    <p>
      <em>Not:</em> Zorunlu çerezleri devre dışı bırakmak, platformun çalışmamasına neden olabilir.
    </p>

    <h2>6. Üçüncü Taraf Çerezleri</h2>
    <p>
      Platformumuzda &ldquo;Buy Me a Coffee&rdquo; butonu gibi üçüncü taraf hizmetleri bulunabilir.
      Bu hizmetler kendi çerezlerini yerleştirebilir. Bu çerezlerin kullanımı ilgili
      hizmetin gizlilik politikasına tabidir.
    </p>

    <h2>7. Çocuklar ve Çerezler</h2>
    <p>
      Çocuklara yönelik sayfalarımızda, zorunlu oturum ve güvenlik çerezleri dışında
      reklam veya takip çerezi kullanılmamaktadır. Analitik çerezler yalnızca anonim
      veri toplamak amacıyla ve veli onayı dahilinde kullanılır.
    </p>

    <h2>8. Politika Değişiklikleri</h2>
    <p>
      Bu çerez politikası gerektiğinde güncellenebilir. Değişiklikler bu sayfada yayımlanır.
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
