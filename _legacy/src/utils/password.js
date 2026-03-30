const crypto = require('crypto');

function generateStudentPassword(length = 12) {
  const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*';
  const randomBytes = crypto.randomBytes(length);

  return Array.from(randomBytes, (byte) => alphabet[byte % alphabet.length]).join('');
}

module.exports = {
  generateStudentPassword,
};
