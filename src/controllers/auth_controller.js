const { StudentMailService } = require('../services/student_mail_service');

function buildQrCodeLink(email, password) {
  const qrPayload = JSON.stringify({ email, password });
  return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(qrPayload)}`;
}

function isValidEmail(value) {
  if (!value) {
    return true;
  }

  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function createAuthController(service = new StudentMailService()) {
  return {
    health(_req, res) {
      return res.status(200).json({
        ok: true,
        provider: 'mailcow',
      });
    },

    async createStudent(req, res) {
      try {
        const { firstName, lastName, nickname, parentEmail } = req.body || {};
        const normalizedFirstName = String(firstName || '').trim();
        const normalizedLastName = String(lastName || '').trim();
        const normalizedNickname = String(nickname || '').trim();
        const normalizedParentEmail = String(parentEmail || '').trim().toLowerCase();

        if (!normalizedFirstName || !normalizedLastName) {
          return res.status(400).json({
            ok: false,
            message: 'firstName ve lastName zorunludur.',
          });
        }

        if (!isValidEmail(normalizedParentEmail)) {
          return res.status(400).json({
            ok: false,
            message: 'parentEmail geçerli bir e-posta olmalıdır.',
          });
        }

        const studentMailbox = await service.createStudentMailbox({
          firstName: normalizedFirstName,
          lastName: normalizedLastName,
          nickname: normalizedNickname,
          parentEmail: normalizedParentEmail || null,
        });

        return res.status(201).json({
          ok: true,
          student: {
            ...studentMailbox,
            qrCodeUrl: buildQrCodeLink(studentMailbox.email, studentMailbox.password),
          },
        });
      } catch (error) {
        const statusCode = error.statusCode >= 400 && error.statusCode < 600 ? error.statusCode : 500;

        return res.status(statusCode).json({
          ok: false,
          message: error.message,
        });
      }
    },
  };
}

module.exports = {
  createAuthController,
  buildQrCodeLink,
  isValidEmail,
};
