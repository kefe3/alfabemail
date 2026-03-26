const { StudentMailService } = require('../services/student_mail_service');

function buildQrCodeLink(email, password) {
  const qrPayload = JSON.stringify({ email, password });
  return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(qrPayload)}`;
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

        if (!firstName || !lastName) {
          return res.status(400).json({
            ok: false,
            message: 'firstName ve lastName zorunludur.',
          });
        }

        const studentMailbox = await service.createStudentMailbox({
          firstName,
          lastName,
          nickname,
          parentEmail,
        });

        return res.status(201).json({
          ok: true,
          student: {
            ...studentMailbox,
            qrCodeUrl: buildQrCodeLink(studentMailbox.email, studentMailbox.password),
          },
        });
      } catch (error) {
        return res.status(500).json({
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
};
