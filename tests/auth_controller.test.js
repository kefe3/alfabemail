const test = require('node:test');
const assert = require('node:assert/strict');

const { createAuthController, isValidEmail } = require('../src/controllers/auth_controller');

function createRes() {
  return {
    code: 200,
    payload: null,
    status(code) {
      this.code = code;
      return this;
    },
    json(body) {
      this.payload = body;
      return this;
    },
  };
}

test('isValidEmail boş değeri opsiyonel kabul eder', () => {
  assert.equal(isValidEmail(''), true);
  assert.equal(isValidEmail(null), true);
  assert.equal(isValidEmail('veli@example.com'), true);
  assert.equal(isValidEmail('yanlis-email'), false);
});

test('createStudent invalid parentEmail için 400 döner', async () => {
  const controller = createAuthController({
    createStudentMailbox: async () => {
      throw new Error('should not run');
    },
  });

  const res = createRes();

  await controller.createStudent(
    {
      body: {
        firstName: 'Ada',
        lastName: 'Lovelace',
        parentEmail: 'invalid',
      },
    },
    res,
  );

  assert.equal(res.code, 400);
  assert.equal(res.payload.ok, false);
});

test('createStudent upstream statusCode bilgisini korur', async () => {
  const controller = createAuthController({
    createStudentMailbox: async () => {
      const error = new Error('duplicate mailbox');
      error.statusCode = 409;
      throw error;
    },
  });

  const res = createRes();

  await controller.createStudent(
    {
      body: {
        firstName: 'Ada',
        lastName: 'Lovelace',
      },
    },
    res,
  );

  assert.equal(res.code, 409);
  assert.equal(res.payload.message, 'duplicate mailbox');
});
