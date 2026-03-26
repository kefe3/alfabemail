const { generateStudentPassword } = require('../utils/password');

class StudentMailService {
  constructor({
    apiBaseUrl = process.env.MAILCOW_API_BASE_URL,
    apiKey = process.env.MAILCOW_API_KEY,
    mailDomain = process.env.MAILCOW_DOMAIN || 'alfabe.co',
    quota = Number(process.env.MAILCOW_DEFAULT_QUOTA_MB || 2048),
  } = {}) {
    this.apiBaseUrl = apiBaseUrl;
    this.apiKey = apiKey;
    this.mailDomain = String(mailDomain || '').trim().toLowerCase();
    this.quota = quota;

    if (!this.apiBaseUrl || !this.apiKey) {
      throw new Error('Mailcow API ayarları eksik. MAILCOW_API_BASE_URL ve MAILCOW_API_KEY tanımlı olmalı.');
    }

    if (!this.mailDomain) {
      throw new Error('Mailcow domain ayarı eksik. MAILCOW_DOMAIN tanımlı olmalı.');
    }

    if (!Number.isFinite(this.quota) || this.quota <= 0) {
      throw new Error('Mailcow kota ayarı geçersiz. MAILCOW_DEFAULT_QUOTA_MB pozitif sayı olmalı.');
    }
  }

  slugify(value = '') {
    return value
      .toLocaleLowerCase('tr-TR')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/ı/g, 'i')
      .replace(/[^a-z0-9]+/g, '.')
      .replace(/(^\.|\.$)+/g, '')
      .replace(/\.{2,}/g, '.');
  }

  createMailboxLocalPart({ firstName, lastName, nickname }) {
    const normalizedFirstName = String(firstName || '').trim();
    const normalizedLastName = String(lastName || '').trim();
    const normalizedNickname = String(nickname || '').trim();

    const base = normalizedNickname || `${normalizedFirstName}.${normalizedLastName}`;
    const localPart = this.slugify(base);

    if (!localPart) {
      throw new Error('Öğrenci adı/soyadı veya rumuz geçersiz.');
    }

    return localPart;
  }

  async request(path, body) {
    const response = await fetch(`${this.apiBaseUrl}${path}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-Key': this.apiKey,
      },
      body: JSON.stringify(body),
    });

    const rawBody = await response.text();
    let payload = null;

    if (rawBody) {
      try {
        payload = JSON.parse(rawBody);
      } catch {
        payload = { message: rawBody };
      }
    }

    if (!response.ok) {
      const message = payload?.msg || payload?.message || 'Mailcow API hatası';
      const error = new Error(message);
      error.statusCode = response.status;
      error.details = payload;
      throw error;
    }

    return payload;
  }

  async createStudentMailbox(student) {
    const localPart = this.createMailboxLocalPart(student);
    const username = `${localPart}@${this.mailDomain}`;
    const password = generateStudentPassword();

    const normalizedFirstName = String(student.firstName || '').trim();
    const normalizedLastName = String(student.lastName || '').trim();

    const mailboxPayload = [{
      local_part: localPart,
      domain: this.mailDomain,
      name: `${normalizedFirstName} ${normalizedLastName}`.trim(),
      quota: this.quota,
      password,
      password2: password,
      active: '1',
    }];

    await this.request('/api/v1/add/mailbox', mailboxPayload);

    return {
      fullName: `${normalizedFirstName} ${normalizedLastName}`.trim(),
      email: username,
      password,
      parentEmail: student.parentEmail || null,
    };
  }
}

module.exports = {
  StudentMailService,
};
