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

  const sessionKey = `alfabe_session_${role}`;
  const expected = ROLE_CREDENTIALS[role];
  const active = JSON.parse(sessionStorage.getItem(sessionKey) || 'null');

  function addLogoutBanner(email) {
    const bar = document.createElement('div');
    bar.style.cssText = 'position:fixed;top:10px;right:10px;z-index:9999;background:#111827;color:#fff;padding:8px 10px;border-radius:10px;font:600 12px Inter,sans-serif;display:flex;gap:8px;align-items:center;';
    bar.innerHTML = `<span>${email} ile giriş yapıldı</span><button id="demoLogoutBtn" style="border:none;border-radius:999px;padding:6px 10px;cursor:pointer;font-weight:700;">Çıkış Yap</button>`;
    document.body.appendChild(bar);
    bar.querySelector('#demoLogoutBtn').addEventListener('click', () => {
      sessionStorage.removeItem(sessionKey);
      location.reload();
    });
  }

  if (!active) {
    const wrap = document.createElement('div');
    wrap.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.72);display:grid;place-items:center;z-index:10000;';
    wrap.innerHTML = `
      <form id="demoLoginForm" style="width:min(420px,92vw);background:#fff;border-radius:14px;padding:16px;box-shadow:0 20px 35px rgba(0,0,0,.25);font-family:Inter,sans-serif;">
        <h3 style="margin:0 0 4px;">Demo Giriş (${role})</h3>
        <p style="margin:0 0 10px;color:#64748b;font-size:13px;">Bu girişler geçici demo amaçlıdır.</p>
        <label style="display:block;font-size:12px;margin-bottom:4px;">E-posta</label>
        <input id="demoEmail" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:9px;margin-bottom:8px;" placeholder="${expected.email}" />
        <label style="display:block;font-size:12px;margin-bottom:4px;">Şifre</label>
        <input id="demoPassword" type="password" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:9px;margin-bottom:10px;" placeholder="${expected.password}" />
        <button style="width:100%;border:none;background:#2563eb;color:#fff;padding:10px;border-radius:10px;font-weight:700;cursor:pointer;">Giriş Yap</button>
        <p id="demoHint" style="margin:10px 0 0;color:#475569;font-size:12px;">Demo: ${expected.email} / ${expected.password}</p>
      </form>`;
    document.body.appendChild(wrap);
    wrap.querySelector('#demoLoginForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const email = wrap.querySelector('#demoEmail').value.trim().toLowerCase();
      const password = wrap.querySelector('#demoPassword').value.trim();
      if (email === expected.email && password === expected.password) {
        sessionStorage.setItem(sessionKey, JSON.stringify({ email }));
        wrap.remove();
        addLogoutBanner(email);
      } else {
        wrap.querySelector('#demoHint').textContent = `Hatalı giriş. Demo: ${expected.email} / ${expected.password}`;
        wrap.querySelector('#demoHint').style.color = '#b91c1c';
      }
    });
  } else {
    addLogoutBanner(active.email);
  }
})();
