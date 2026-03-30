<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ALFABE Portal | Karşılama</title>
  <style>
    :root {
      --bg-1: #f4f8ff; --bg-2: #e8fff5; --ink: #224261;
      --muted: #6586a7; --primary: #7fa7ff;
      --card: #ffffff; --shadow: 0 18px 30px rgba(34,66,97,.16);
    }
    * { box-sizing: border-box; }
    body {
      margin: 0; font-family: 'Nunito','Segoe UI',sans-serif;
      color: var(--ink); min-height: 100vh; overflow-x: hidden;
      background: radial-gradient(circle at 15% 12%, #fff 5%, var(--bg-1) 45%, var(--bg-2) 100%);
    }

    /* ── SAHNE ─────────────────────────────────────────── */
    .scene {
      position: relative; width: min(1200px,96vw);
      margin: 28px auto 0; height: 230px;
    }
    .ice-ground {
      position: absolute; left: 2%; right: 2%; bottom: 10px;
      height: 30px; border-radius: 99px;
      background: linear-gradient(90deg,#d8ebff,#c4ffe7);
    }

    /* ── KAPI ──────────────────────────────────────────── */
    .door {
      position: absolute; right: 12%; bottom: 36px;
      width: 148px; height: 174px;
      border-radius: 18px 18px 5px 5px;
      background: linear-gradient(180deg,#8faeff,#6f8fe8);
      box-shadow: var(--shadow); overflow: hidden; isolation: isolate;
    }
    .door-inside {
      position: absolute; inset: 12px; border-radius: 10px;
      background: radial-gradient(circle at 55% 35%,#8fc2ff,#274780 72%);
      opacity: .82;
    }
    .door-brand {
      position: absolute; left: 0; right: 0; top: 18px; z-index: 3;
      text-align: center; font-weight: 800; letter-spacing: .5px;
      color: rgba(255,255,255,.92); text-shadow: 0 1px 0 rgba(0,0,0,.15);
    }
    .door-frame {
      position: absolute; inset: 10px; border-radius: 12px;
      border: 2px dashed rgba(255,255,255,.68);
    }
    .door-leaf {
      position: absolute; inset: 12px 14px 12px 12px; border-radius: 11px;
      background: linear-gradient(180deg,#7396f1,#597ed8);
      transform-origin: left center;
      animation: door-open 9s ease-in-out infinite; z-index: 2;
    }
    .door-knob {
      position: absolute; right: 22px; top: 76px;
      width: 12px; height: 12px; border-radius: 50%; background: #ffe8ad;
    }

    /* ── PENGUENİ ──────────────────────────────────────── */
    .penguin {
      position: absolute; bottom: 23px; left: -140px;
      width: 130px; height: 160px;
      animation: penguin-walk 9s linear infinite;
    }
    .penguin .body {
      position: absolute; left: 22px; top: 32px;
      width: 86px; height: 106px; border-radius: 45px;
      background: #2e2529; border: 4px solid #4b3d42;
    }
    .penguin .belly {
      position: absolute; left: 36px; top: 50px;
      width: 58px; height: 82px; border-radius: 31px; background: #f2f3f7;
    }
    .penguin .head {
      position: absolute; left: 29px; top: 12px;
      width: 72px; height: 60px; border-radius: 50%;
      background: #2e2529; border: 4px solid #4b3d42;
    }
    .penguin .eye,.penguin .eye.right {
      position: absolute; top: 24px; left: 44px;
      width: 18px; height: 23px; border-radius: 45%; background: #fff;
    }
    .penguin .eye.right { left: 70px; }
    .penguin .eye::after,.penguin .eye.right::after {
      content: ''; position: absolute;
      width: 8px; height: 8px; border-radius: 50%;
      background: #111; top: 8px; left: 6px;
    }
    .penguin .beak {
      position: absolute; left: 61px; top: 46px;
      width: 18px; height: 16px;
      background: #ecd923; border-radius: 0 0 10px 10px;
      border: 2px solid #27210f; z-index: 4;
    }

    /* Kanatlar */
    .penguin .wing-left {
      position: absolute; left: 2px; top: 28px;
      width: 30px; height: 62px; border-radius: 20px;
      background: #2e2529; border: 3px solid #4b3d42;
      transform-origin: top center;
      animation: wing-carry 9s ease-in-out infinite;
    }
    .penguin .wing-right {
      position: absolute; right: 8px; top: 52px;
      width: 28px; height: 58px; border-radius: 20px;
      background: #2e2529; border: 3px solid #4b3d42;
      transform: rotate(24deg);
    }

    /* Penguen elindeki postalar */
    .penguin .mail-stack {
      position: absolute; left: -6px; top: 52px; z-index: 6;
      animation: mail-carry 9s ease-in-out infinite;
    }
    .penguin .mail {
      position: absolute; width: 28px; height: 18px;
      border-radius: 4px; background: #fff;
      border: 2px solid #8ca2c8;
    }
    .penguin .mail::before {
      content: ''; position: absolute;
      left: 2px; right: 2px; top: 2px; height: 0;
      border-top: 8px solid #e9f1ff;
      border-left: 9px solid transparent;
      border-right: 9px solid transparent;
    }
    .penguin .mail.one  { top: 0;  left: 0;  transform: rotate(-8deg); }
    .penguin .mail.two  { top: 6px; left: 4px; transform: rotate(5deg); }
    .penguin .mail.three{ top: 12px; left: 2px; transform: rotate(-3deg); }

    .penguin .foot,.penguin .foot.right {
      position: absolute; bottom: 0;
      width: 37px; height: 22px; border-radius: 50%;
      background: #eee024; transform: rotate(-18deg);
    }
    .penguin .foot { left: 28px; }
    .penguin .foot.right { right: 20px; transform: rotate(12deg); }
    .penguin .shadow {
      position: absolute; left: 35px; bottom: -8px;
      width: 62px; height: 11px; border-radius: 50%;
      background: rgba(27,43,68,.22);
    }

    /* ── ANİMASYONLAR ──────────────────────────────────── */
    /* Penguen yürüyüşü + son bakış:
       0-55%  : sola doğru yürür, mailleri taşır
       55-72% : kapıya yaklaşır, yavaşlar
       72-79% : SON BAKIŞ — hafifçe geriye döner (rotateY)
       79-90% : kapıya girer, scale küçülür
       90-100%: tamamen kaybolur                        */
    @keyframes penguin-walk {
      0%   { left: -140px; transform: scaleX(1) translateY(0) scale(1); opacity: 1; }
      55%  { left: calc(88% - 210px); transform: scaleX(1) translateY(-2px) scale(1); opacity: 1; }
      72%  { left: calc(88% - 130px); transform: scaleX(1) translateY(-1px) scale(0.98); opacity: 1; }
      /* Son bakış: penguen hafif sağa (geriye) döner */
      76%  { left: calc(88% - 118px); transform: scaleX(-1) translateY(-1px) scale(0.95); opacity: 1; }
      79%  { left: calc(88% - 118px); transform: scaleX(1) translateY(-1px) scale(0.93); opacity: 1; }
      /* Kapıya giriş */
      90%  { left: calc(88% - 74px);  transform: scaleX(1) translateY(-2px) scale(0.72); opacity: 0.7; }
      100% { left: calc(88% - 70px);  transform: scaleX(1) translateY(-2px) scale(0.55); opacity: 0; }
    }

    @keyframes door-open {
      0%, 60%   { transform: perspective(500px) rotateY(0deg); }
      72%, 100% { transform: perspective(500px) rotateY(-78deg); }
    }

    /* Sol kanat yukarı kaldırılmış — taşıma pozisyonu */
    @keyframes wing-carry {
      0%, 72%  { transform: rotate(-55deg); }
      76%, 79% { transform: rotate(-30deg); }  /* son bakışta kanat gevşer */
      80%, 100%{ transform: rotate(-55deg); }
    }

    /* Mail stack sol kanada sabitli kalır */
    @keyframes mail-carry {
      0%, 72%  { opacity: 1; transform: rotate(0deg); }
      90%      { opacity: 0.4; }
      100%     { opacity: 0; }
    }

    @keyframes floaty {
      0%,100% { transform: translateY(0); }
      50%     { transform: translateY(-6px); }
    }
    @keyframes pulse-glow {
      0%,100% { box-shadow: 0 0 0 0 rgba(94,141,247,.35); }
      50%     { box-shadow: 0 0 26px 8px rgba(94,141,247,.45); }
    }
    @keyframes student-cta-bounce {
      0%,100% { transform: translateY(0); }
      50%     { transform: translateY(-4px); }
    }

    /* ── KARTLAR ───────────────────────────────────────── */
    .entry { width: min(1200px,96vw); margin: 14px auto 38px; text-align: center; }
    h1 { margin: 0; font-size: clamp(24px,4vw,42px); }
    .sub { margin: 8px 0 22px; color: var(--muted); }
    .grid {
      display: grid;
      grid-template-columns: 1fr minmax(300px,1.2fr) 1fr;
      gap: 16px; align-items: stretch;
    }
    .card {
      border-radius: 20px; background: var(--card);
      box-shadow: var(--shadow); padding: 18px;
      position: relative; overflow: hidden;
      animation: floaty 3s ease-in-out infinite;
    }
    .card::before {
      content: ''; position: absolute;
      inset: -70% auto auto -30%; width: 180%; height: 180%;
      background: radial-gradient(circle,rgba(255,255,255,.36),transparent 63%);
    }
    .card h2 { margin: 0 0 8px; position: relative; }
    .card p  { margin: 0 0 14px; color: var(--muted); position: relative; }
    .card button {
      border: none; border-radius: 999px; padding: 10px 16px;
      font-weight: 700; cursor: pointer; position: relative;
    }

    .student {
      background: linear-gradient(150deg,#9bc9ff,#cde4ff);
      transform: scale(1.06); animation-delay: .2s;
      animation: floaty 3s ease-in-out infinite, pulse-glow 2s ease-in-out infinite;
      display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
    }
    .student button {
      background: #5e8df7; color: #fff; font-size: 20px;
      padding: 16px 24px; width: min(92%,330px); margin: 14px auto 0;
      display: block; animation: student-cta-bounce 1.8s ease-in-out infinite;
    }
    .manager { background: linear-gradient(150deg,#dbe9ff,#eef4ff); }
    .manager button { background: #4285F4; color: #fff; }
    .teacher { background: linear-gradient(150deg,#ffe2de,#ffeceb); animation-delay: .4s; }
    .teacher button { background: #EA4335; color: #fff; }
    .parent  { background: linear-gradient(150deg,#dff6e6,#edfbf1); animation-delay: .6s; }
    .parent button { background: #34A853; color: #fff; }
    .portal-info { background: linear-gradient(150deg,#f0e6ff,#f8f2ff); }
    .portal-info button { background: #A142F4; color: #fff; }
    .left-col,.right-col { display: grid; gap: 16px; }

    /* ── FOOTER ────────────────────────────────────────── */
    .footer { margin-top: 36px; background: #112642; color: #e8f0ff; padding: 34px 0; }
    .footer-wrap {
      width: min(1200px,96vw); margin: 0 auto;
      display: grid; grid-template-columns: repeat(4,1fr);
      gap: 18px; text-align: left;
    }
    .footer h3 { margin: 0 0 10px; font-size: 18px; }
    .footer p,.footer a,.footer li { color: #d1deff; font-size: 14px; line-height: 1.55; text-decoration: none; }
    .footer ul { margin: 0; padding-left: 16px; }
    .socials { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
    .socials a {
      background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.2);
      border-radius: 999px; padding: 4px 10px;
    }
    .footer-bottom { width: min(1200px,96vw); margin: 20px auto 0; text-align: center; }
    .copyright { margin-top: 8px; color: #c7d7ff; font-size: 13px; }

    @media (max-width: 920px) {
      .grid { grid-template-columns: 1fr; }
      .footer-wrap { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- Penguen Animasyonu -->
  <section class="scene" aria-label="Kayıp penguen animasyonu">
    <div class="ice-ground"></div>
    <div class="door" title="Portal Giriş Kapısı">
      <div class="door-inside"></div>
      <div class="door-brand">alfabe.co</div>
      <div class="door-frame"></div>
      <div class="door-leaf" id="doorLeaf"><div class="door-knob"></div></div>
    </div>
    <div class="penguin" id="penguin">
      <div class="wing-left"></div>
      <!-- Mail yığını sol kola bağlı -->
      <div class="mail-stack">
        <div class="mail one"></div>
        <div class="mail two"></div>
        <div class="mail three"></div>
      </div>
      <div class="wing-right"></div>
      <div class="head"></div>
      <div class="eye"></div>
      <div class="eye right"></div>
      <div class="beak"></div>
      <div class="body"></div>
      <div class="belly"></div>
      <div class="foot"></div>
      <div class="foot right"></div>
      <div class="shadow"></div>
    </div>
  </section>

  <!-- Giriş Kartları -->
  <main class="entry">
    <h1>🐧 Kayıp Penguen ALFABE Portalı Buldu!</h1>
    <p class="sub">Alfabe — çocukların güvenle kullanabilecekleri eMail sistemi.</p>

    <div class="grid">
      <div class="left-col">
        <article class="card manager">
          <h2>🏫 Yönetici</h2>
          <p>Okul yönetimi, sınıf ve öğretmen organizasyonu.</p>
          <button onclick="go('{{ route('filament.portal.auth.login') }}')">Yönetici Paneli</button>
        </article>
        <article class="card teacher">
          <h2>🧑🏫 Öğretmen</h2>
          <p>Öğrenci kaydı, kart basımı ve süreç yönetimi.</p>
          <button onclick="go('{{ route('filament.portal.auth.login') }}')">Öğretmen Paneli</button>
        </article>
      </div>

      <article class="card student">
        <h2>🎒 Öğrenci Girişi</h2>
        <p>Karekod ile hızlı ve eğlenceli erişim.</p>
        <button onclick="go('{{ route('ogrenci.giris') }}')">Öğrenci Girişi Yap</button>
      </article>

      <div class="right-col">
        <article class="card parent">
          <h2>👨👩👧 Veli</h2>
          <p>Öğrenci gelişimi ve etkinlik özet raporları.</p>
          <button onclick="go('{{ route('filament.admin.auth.login') }}')">Veli Girişi</button>
        </article>
        <article class="card portal-info">
          <h2>⚙️ Admin</h2>
          <p>Süper Admin ve Admin yönetim paneli.</p>
          <button onclick="go('{{ route('filament.admin.auth.login') }}')">Admin Paneli</button>
        </article>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-wrap">
      <section>
        <h3>ALFABE</h3>
        <p>Çocuklar için güvenli eMail dünyası.</p>
        <div class="socials">
          <a href="#">Instagram</a><a href="#">X</a>
          <a href="#">LinkedIn</a><a href="#">YouTube</a>
        </div>
      </section>
      <section>
        <h3>Hızlı Linkler</h3>
        <ul>
          <li><a href="{{ url('/') }}">Ana Sayfa</a></li>
          <li><a href="{{ route('ogrenci.giris') }}">Öğrenci Girişi</a></li>
          <li><a href="{{ route('filament.portal.auth.login') }}">Öğretmen/Yönetici</a></li>
          <li><a href="{{ route('filament.admin.auth.login') }}">Admin Paneli</a></li>
        </ul>
      </section>
      <section>
        <h3>Adres</h3>
        <p>Konya Kapsül</p>
        <p>E-posta: iletisim@alfabe.co</p>
        <p>Telefon: +90 (332) 000 00 00</p>
      </section>
      <section>
        <h3>Yasal</h3>
        <ul>
          <li><a href="#">KVKK</a></li>
          <li><a href="#">Gizlilik Politikası</a></li>
          <li><a href="#">Kullanım Şartları</a></li>
          <li><a href="#">Çerez Politikası</a></li>
        </ul>
      </section>
    </div>
    <div class="footer-bottom">
      <div class="copyright">Copyright &copy; {{ date('Y') }} Alfabe Mail. Tüm hakları saklıdır.</div>
    </div>
  </footer>

  <script>
    const penguin  = document.getElementById('penguin');
    const doorLeaf = document.getElementById('doorLeaf');

    function restartAnim(el, anim) {
      el.style.animation = 'none';
      requestAnimationFrame(() => requestAnimationFrame(() => { el.style.animation = anim; }));
    }

    function restartPenguin() {
      restartAnim(penguin,  'penguin-walk 9s linear infinite');
      restartAnim(doorLeaf, 'door-open 9s ease-in-out infinite');
    }

    function go(path) { window.location.href = path; }
  </script>
</body>
</html>
