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
    this.mailDomain = mailDomain;
    this.quota = quota;

    if (!this.apiBaseUrl || !this.apiKey) {
      throw new Error('Mail API ayarları eksik. MAILCOW_API_BASE_URL ve MAILCOW_API_KEY tanımlı olmalı.');
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
    const base = nickname || `${firstName}.${lastName}`;
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

    const payload = await response.json();

    if (!response.ok) {
      const message = payload?.msg || payload?.message || 'Mail API hatası';
      throw new Error(message);
    }

    return payload;
  }

  async createStudentMailbox(student) {
    const localPart = this.createMailboxLocalPart(student);
    const username = `${localPart}@${this.mailDomain}`;
    const password = generateStudentPassword();

    const mailboxPayload = [{
      local_part: localPart,
      domain: this.mailDomain,
      name: `${student.firstName} ${student.lastName}`,
      quota: this.quota,
      password,
      password2: password,
      active: '1',
    }];

    await this.request('/api/v1/add/mailbox', mailboxPayload);

    return {
      fullName: `${student.firstName} ${student.lastName}`,
      email: username,
      password,
      parentEmail: student.parentEmail || null,
    };
  }
}

module.exports = {
  StudentMailService,
};
