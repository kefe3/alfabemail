const test = require('node:test');
const assert = require('node:assert/strict');

const { StudentMailService } = require('../src/services/student_mail_service');

test('createMailboxLocalPart türkçe karakterleri normalize eder', () => {
  const service = new StudentMailService({
    apiBaseUrl: 'http://mailcow.local',
    apiKey: 'test-key',
    mailDomain: 'alfabe.co',
  });

  const localPart = service.createMailboxLocalPart({
    firstName: 'Çağrı',
    lastName: 'Işık',
  });

  assert.equal(localPart, 'cagri.isik');
});

test('request Mailcow hata cevabını statusCode ile fırlatır', async () => {
  const service = new StudentMailService({
    apiBaseUrl: 'http://mailcow.local',
    apiKey: 'test-key',
    mailDomain: 'alfabe.co',
  });

  global.fetch = async () => ({
    ok: false,
    status: 409,
    text: async () => JSON.stringify({ msg: 'already exists' }),
  });

  await assert.rejects(
    service.request('/api/v1/add/mailbox', []),
    (error) => error.message === 'already exists' && error.statusCode === 409,
  );
});

test('createStudentMailbox normalize edilmiş adla öğrenci döner', async () => {
  const service = new StudentMailService({
    apiBaseUrl: 'http://mailcow.local',
    apiKey: 'test-key',
    mailDomain: 'Alfabe.CO',
  });

  service.request = async () => ({ ok: true });

  const student = await service.createStudentMailbox({
    firstName: ' Ada ',
    lastName: ' Lovelace ',
    parentEmail: 'veli@example.com',
  });

  assert.equal(student.email, 'ada.lovelace@alfabe.co');
  assert.equal(student.fullName, 'Ada Lovelace');
  assert.equal(student.parentEmail, 'veli@example.com');
  assert.ok(student.password.length > 0);
});
