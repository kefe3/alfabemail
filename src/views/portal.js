const dom = {
  tabs: document.querySelectorAll('.tab-btn'),
  panels: document.querySelectorAll('.panel'),
  firstName: document.getElementById('firstName'),
  lastName: document.getElementById('lastName'),
  parentEmail: document.getElementById('parentEmail'),
  username: document.getElementById('username'),
  saveBtn: document.getElementById('saveStudentBtn'),
  printBtn: document.getElementById('printBtn'),
  status: document.getElementById('statusBox'),
  badgeWrap: document.getElementById('badgePreviewWrap'),
  badgeName: document.getElementById('badgeName'),
  badgeEmail: document.getElementById('badgeEmail'),
  badgePassword: document.getElementById('badgePassword'),
  badgeQr: document.getElementById('badgeQr'),
  adminTotalStudents: document.getElementById('adminTotalStudents'),
  adminTotalTeachers: document.getElementById('adminTotalTeachers'),
  teacherList: document.getElementById('teacherList'),
  studentLoginInfo: document.getElementById('studentLoginInfo'),
  parentInfo: document.getElementById('parentInfo'),
};

const appState = {
  students: [],
  teachers: [
    { name: 'Ayşe Öğretmen', class: '2-A' },
    { name: 'Mert Öğretmen', class: '3-B' },
  ],
};

function slugify(value = '') {
  return value
    .toLocaleLowerCase('tr-TR')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/ı/g, 'i')
    .replace(/[^a-z0-9]+/g, '.')
    .replace(/(^\.|\.$)+/g, '')
    .replace(/\.{2,}/g, '.');
}

function generatePassword(length = 12) {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*';
  const arr = new Uint32Array(length);
  window.crypto.getRandomValues(arr);
  return Array.from(arr, n => chars[n % chars.length]).join('');
}

function buildQrCodeLink(email, password) {
  const payload = JSON.stringify({ email, password });
  return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(payload)}`;
}

function suggestUsername() {
  const firstName = dom.firstName.value.trim();
  const lastName = dom.lastName.value.trim();
  if (!firstName || !lastName) return;
  const suggestion = `${slugify(`${firstName}.${lastName}`)}@alfabe.co`;
  if (!dom.username.dataset.edited) dom.username.value = suggestion;
}

function markUsernameEdited() {
  dom.username.dataset.edited = '1';
}

function setStatus(message) {
  dom.status.textContent = message;
}

function simulateMailcowCreate(studentPayload) {
  return new Promise((resolve) => {
    setTimeout(() => {
      const password = generatePassword();
      resolve({
        ...studentPayload,
        password,
        qrCodeUrl: buildQrCodeLink(studentPayload.email, password),
      });
    }, 900);
  });
}

function renderDashboards() {
  dom.adminTotalStudents.textContent = String(appState.students.length);
  dom.adminTotalTeachers.textContent = String(appState.teachers.length);

  dom.teacherList.innerHTML = appState.teachers
    .map(t => `<li>${t.name} - <strong>${t.class}</strong></li>`)
    .join('');

  const latest = appState.students[appState.students.length - 1];
  dom.studentLoginInfo.textContent = latest
    ? `Son oluşturulan öğrenci hesabı: ${latest.fullName} (${latest.email})`
    : 'Henüz öğrenci hesabı oluşturulmadı.';

  dom.parentInfo.textContent = latest
    ? `${latest.parentEmail || 'Veli maili yok'} adresine özet bildirim tanımlı.`
    : 'Veli paneli için henüz eşleştirilmiş öğrenci yok.';
}

async function saveStudent() {
  const firstName = dom.firstName.value.trim();
  const lastName = dom.lastName.value.trim();
  const parentEmail = dom.parentEmail.value.trim();
  const email = dom.username.value.trim();

  if (!firstName || !lastName || !parentEmail || !email) {
    setStatus('Lütfen ad, soyad, veli maili ve kullanıcı adı alanlarını doldurun.');
    return;
  }

  dom.saveBtn.disabled = true;
  setStatus('Mailcow API simülasyonu çalışıyor... öğrenci hesabı açılıyor.');

  const student = await simulateMailcowCreate({
    fullName: `${firstName} ${lastName}`,
    firstName,
    lastName,
    parentEmail,
    email,
  });

  appState.students.push(student);

  dom.badgeName.textContent = student.fullName;
  dom.badgeEmail.textContent = student.email;
  dom.badgePassword.textContent = student.password;
  dom.badgeQr.src = student.qrCodeUrl;
  dom.badgeQr.alt = `${student.fullName} karekod`;
  dom.badgeWrap.classList.add('visible');
  dom.printBtn.disabled = false;

  setStatus(`Öğrenci kaydedildi: ${student.fullName}. Mail hesabı oluşturuldu (simülasyon).`);
  renderDashboards();
  dom.saveBtn.disabled = false;
}

function printBadge() {
  window.print();
}

function switchTab(nextTab) {
  dom.tabs.forEach(btn => btn.classList.toggle('active', btn.dataset.tab === nextTab));
  dom.panels.forEach(panel => panel.classList.toggle('active', panel.id === `${nextTab}Panel`));
}

function bindEvents() {
  dom.firstName.addEventListener('input', suggestUsername);
  dom.lastName.addEventListener('input', suggestUsername);
  dom.username.addEventListener('input', markUsernameEdited);
  dom.saveBtn.addEventListener('click', saveStudent);
  dom.printBtn.addEventListener('click', printBadge);
  dom.tabs.forEach(btn => btn.addEventListener('click', () => switchTab(btn.dataset.tab)));
}

bindEvents();
renderDashboards();
setStatus('Öğrenci eklemek için formu doldurun.');
