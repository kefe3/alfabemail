(function () {
  const ROLE_CREDENTIALS = {
    super_admin: { email: 'info@ismailcimen.com.tr', password: 'Demo123!' },
    admin: { email: 'admin@alfabe.co', password: 'Demo123!' },
    bayi: { email: 'bayi@alfabe.co', password: 'Demo123!' },
    yonetici: { email: 'yonetici@alfabe.co', password: 'Demo123!' },
    ogretmen: { email: 'ogretmen@alfabe.co', password: 'Demo123!' },
    veli: { email: 'veli@alfabe.co', password: 'Demo123!' },
    ogrenci: { email: 'ogrenci@alfabe.co', password: 'Demo123!' },
  };

  const role = document.body?.dataset?.role;
  if (!role || !ROLE_CREDENTIALS[role]) return;
  if (role === 'ogrenci') return;

  const sessionKey = `alfabe_session_${role}`;
  const expected = ROLE_CREDENTIALS[role];
  const active = JSON.parse(sessionStorage.getItem(sessionKey) || 'null');

  function addLogoutBar(email) {
    const bar = document.createElement('header');
    bar.style.cssText = 'position:sticky;top:0;z-index:9999;background:#0f172a;color:#fff;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;font:600 13px Inter,sans-serif;';
    bar.innerHTML = `<span>${email} ile giriş yapıldı</span><button id="demoLogoutBtn" style="border:none;border-radius:999px;padding:7px 12px;cursor:pointer;font-weight:700;background:#facc15;color:#111827;">Çıkış Yap</button>`;
    document.body.prepend(bar);
    bar.querySelector('#demoLogoutBtn').addEventListener('click', () => {
      sessionStorage.removeItem(sessionKey);
      location.reload();
    });
  }

  function renderFullPageLogin() {
    document.body.innerHTML = `
      <main style="min-height:100vh;display:grid;place-items:center;background:linear-gradient(160deg,#e6efff,#f5fbff);padding:16px;">
        <form id="demoLoginForm" style="width:min(440px,94vw);background:#fff;border:1px solid #dbe5f5;border-radius:16px;padding:18px;box-shadow:0 15px 35px rgba(15,23,42,.12);font-family:Inter,sans-serif;">
          <h2 style="margin:0 0 4px;color:#0f172a;">${role} Giriş Sayfası</h2>
          <p style="margin:0 0 12px;color:#475569;font-size:13px;">Demo giriş bilgisi ile oturum açabilirsiniz.</p>

          <label style="display:block;font-size:12px;color:#475569;margin-bottom:4px;">E-posta</label>
          <input id="demoEmail" type="email" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:9px;margin-bottom:8px;" placeholder="${expected.email}" />

          <label style="display:block;font-size:12px;color:#475569;margin-bottom:4px;">Şifre</label>
          <input id="demoPassword" type="password" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:9px;margin-bottom:12px;" placeholder="${expected.password}" />

          <button style="width:100%;border:none;background:#2563eb;color:#fff;padding:11px;border-radius:10px;font-weight:700;cursor:pointer;">Giriş Yap</button>
          <p id="demoHint" style="margin:10px 0 0;color:#475569;font-size:12px;">Demo: ${expected.email} / ${expected.password}</p>
        </form>
      </main>
    `;

    const form = document.getElementById('demoLoginForm');
    const hint = document.getElementById('demoHint');

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = document.getElementById('demoEmail').value.trim().toLowerCase();
      const password = document.getElementById('demoPassword').value.trim();

      if (email === expected.email && password === expected.password) {
        sessionStorage.setItem(sessionKey, JSON.stringify({ email }));
        location.reload();
      } else {
        hint.textContent = `Hatalı giriş. Demo: ${expected.email} / ${expected.password}`;
        hint.style.color = '#b91c1c';
      }
    });
  }

  if (!active) {
    renderFullPageLogin();
    return;
  }

  addLogoutBar(active.email);
})();
