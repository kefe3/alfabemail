<div id="kayitModal" style="display:none;position:fixed;z-index:9999;inset:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:20px;padding:30px 30px 20px;width:100%;max-width:440px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;">
    <button type="button" onclick="document.getElementById('kayitModal').style.display='none'" style="position:absolute;top:12px;right:16px;border:none;background:none;font-size:28px;cursor:pointer;color:#888;">&times;</button>

    {{-- Step 1: Email --}}
    <div id="kayitStep1">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">🐧</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Hesap Oluştur</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 20px;font-size:14px;">E-posta adresinle başla</p>
      <div style="display:grid;gap:12px;">
        <input type="email" id="kayitEmail" placeholder="E-posta adresin" value="" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <div id="kayitEmailError" style="color:#ef4444;font-size:13px;display:none;"></div>
        <button type="button" onclick="kayitSendCode()" id="kayitBtn1" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Doğrulama Kodu Gönder</button>
      </div>
    </div>

    {{-- Step 2: Code --}}
    <div id="kayitStep2" style="display:none;">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">📧</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Kodu Gir</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 4px;font-size:14px;">E-postana gelen 6 haneli kodu gir</p>
      <p style="text-align:center;color:#eab308;margin:0 0 16px;font-size:13px;font-weight:600;">📧 Kod gelmezse spam/gereksiz klasörünü kontrol et</p>
      <div style="display:grid;gap:12px;">
        <input type="text" id="kayitCode" placeholder="6 haneli kod" maxlength="6" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:24px;width:100%;text-align:center;letter-spacing:8px;font-weight:700;color:#1a202c;">
        <div id="kayitCodeError" style="color:#ef4444;font-size:13px;display:none;"></div>
        <button type="button" onclick="kayitVerifyCode()" id="kayitBtn2" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Doğrula</button>
        <button type="button" onclick="kayitSendCode()" style="background:transparent;border:none;color:#5e8df7;font-size:13px;cursor:pointer;text-align:center;">Kodu tekrar gönder</button>
      </div>
    </div>

    {{-- Step 3: Name + Password --}}
    <div id="kayitStep3" style="display:none;">
      <div style="font-size:40px;text-align:center;margin-bottom:4px;">✅</div>
      <h3 style="text-align:center;margin:0 0 4px;font-size:20px;color:#1a202c;">Son Adım</h3>
      <p style="text-align:center;color:#6586a7;margin:0 0 20px;font-size:14px;">Adın ve şifreni belirle</p>
      <div style="display:grid;gap:12px;">
        <input type="text" id="kayitName" placeholder="Adın Soyadın" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="tel" id="kayitPhone" placeholder="Telefon Numaran" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="text" id="kayitSchool" placeholder="Öğrencinin Okulu" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="password" id="kayitPassword" placeholder="Şifre (en az 6 karakter)" minlength="6" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <input type="password" id="kayitPasswordConfirm" placeholder="Şifre Tekrar" style="padding:12px 14px;border:2px solid #e2e8f0;border-radius:12px;font-size:15px;width:100%;color:#1a202c;">
        <div id="kayitCompleteError" style="color:#ef4444;font-size:13px;display:none;"></div>
        <button type="button" onclick="kayitComplete()" id="kayitBtn3" style="background:#5e8df7;color:#fff;border:none;border-radius:999px;padding:14px;font-size:16px;font-weight:700;cursor:pointer;">Kaydol</button>
      </div>
    </div>

    {{-- Success --}}
    <div id="kayitSuccess" style="display:none;text-align:center;padding:20px 0;">
      <div style="font-size:60px;margin-bottom:10px;">🎉</div>
      <p style="font-size:18px;font-weight:700;margin:0;color:#1a202c;">Kaydın alındı!</p>
      <p style="color:#6586a7;margin:6px 0 0;">Yönetici onayından sonra giriş yapabileceksin.</p>
    </div>
  </div>
</div>

<script>
let kayitEmail = '';

function kayitShowError(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.style.display = 'block';
}

function kayitClearErrors() {
  document.querySelectorAll('[id$=Error]').forEach(e => e.style.display = 'none');
}

async function kayitSendCode() {
  kayitClearErrors();
  const email = document.getElementById('kayitEmail').value.trim();
  if (!email) { kayitShowError('kayitEmailError', 'E-posta adresini gir.'); return; }
  const btn = document.getElementById('kayitBtn1');
  btn.disabled = true; btn.textContent = 'Gönderiliyor...';
  try {
    const res = await fetch('{{ route("kayit.send-code") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ email })
    });
    const data = await res.json();
    if (data.success) {
      kayitEmail = email;
      document.getElementById('kayitStep1').style.display = 'none';
      document.getElementById('kayitStep2').style.display = 'block';
      document.getElementById('kayitCode').value = '';
      document.getElementById('kayitCode').focus();
    } else {
      kayitShowError('kayitEmailError', data.message || 'Bir hata oluştu.');
    }
  } catch(e) { kayitShowError('kayitEmailError', 'Bir hata oluştu.'); }
  finally { btn.disabled = false; btn.textContent = 'Doğrulama Kodu Gönder'; }
}

async function kayitVerifyCode() {
  kayitClearErrors();
  const code = document.getElementById('kayitCode').value.trim();
  if (!code || code.length !== 6) { kayitShowError('kayitCodeError', '6 haneli kodu gir.'); return; }
  const btn = document.getElementById('kayitBtn2');
  btn.disabled = true; btn.textContent = 'Doğrulanıyor...';
  try {
    const res = await fetch('{{ route("kayit.verify-code") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ email: kayitEmail, code })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('kayitStep2').style.display = 'none';
      document.getElementById('kayitStep3').style.display = 'block';
    } else {
      kayitShowError('kayitCodeError', data.message || 'Kod hatalı.');
    }
  } catch(e) { kayitShowError('kayitCodeError', 'Bir hata oluştu.'); }
  finally { btn.disabled = false; btn.textContent = 'Doğrula'; }
}

async function kayitComplete() {
  kayitClearErrors();
  const name = document.getElementById('kayitName').value.trim();
  const phone = document.getElementById('kayitPhone').value.trim();
  const school = document.getElementById('kayitSchool').value.trim();
  const password = document.getElementById('kayitPassword').value;
  const passwordConfirm = document.getElementById('kayitPasswordConfirm').value;
  if (!name) { kayitShowError('kayitCompleteError', 'Adını soyadını gir.'); return; }
  if (!phone) { kayitShowError('kayitCompleteError', 'Telefon numaranı gir.'); return; }
  if (!school) { kayitShowError('kayitCompleteError', 'Öğrencinin okulunu gir.'); return; }
  if (password.length < 6) { kayitShowError('kayitCompleteError', 'Şifre en az 6 karakter olmalı.'); return; }
  if (password !== passwordConfirm) { kayitShowError('kayitCompleteError', 'Şifreler eşleşmiyor.'); return; }
  const btn = document.getElementById('kayitBtn3');
  btn.disabled = true; btn.textContent = 'Kaydediliyor...';
  try {
    const res = await fetch('{{ route("kayit.complete") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ email: kayitEmail, name, phone, school, password, password_confirmation: passwordConfirm })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('kayitStep3').style.display = 'none';
      document.getElementById('kayitSuccess').style.display = 'block';
      setTimeout(() => { location.href = '{{ request()->url() }}'; }, 3000);
    } else {
      kayitShowError('kayitCompleteError', data.message || 'Bir hata oluştu.');
    }
  } catch(e) { kayitShowError('kayitCompleteError', 'Bir hata oluştu.'); }
  finally { btn.disabled = false; btn.textContent = 'Kaydol'; }
}
</script>
