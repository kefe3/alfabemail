const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');

dotenv.config();

const app = express();
const port = Number(process.env.PORT || 3000);

const allowedOrigins = String(process.env.PORTAL_ALLOWED_ORIGINS || '')
  .split(',')
  .map((item) => item.trim())
  .filter(Boolean);

const runtimeConfig = {
  apiBaseUrl: process.env.MAILCOW_API_BASE_URL || '',
  apiKey: process.env.MAILCOW_API_KEY || '',
  mailDomain: process.env.MAILCOW_DOMAIN || 'alfabe.co',
};

app.use(express.json({ limit: '1mb' }));
app.use(express.urlencoded({ extended: false }));

app.use(cors({
  origin(origin, callback) {
    if (!origin) {
      return callback(null, true);
    }

    if (allowedOrigins.includes(origin)) {
      return callback(null, true);
    }

    return callback(new Error('CORS engeli: bu origin için izin yok.'));
  },
}));

app.get('/api/health', (_req, res) => {
  res.status(200).json({ ok: true, service: 'mailcow-proxy' });
});

app.get('/api/mailcow/config/status', (_req, res) => {
  res.status(200).json({
    ok: true,
    configured: Boolean(runtimeConfig.apiBaseUrl && runtimeConfig.apiKey),
    apiBaseUrl: runtimeConfig.apiBaseUrl || null,
    mailDomain: runtimeConfig.mailDomain || null,
    hasApiKey: Boolean(runtimeConfig.apiKey),
  });
});

app.post('/api/mailcow/config', (req, res) => {
  const { apiBaseUrl, apiKey, mailDomain } = req.body || {};

  if (!apiBaseUrl || !apiKey) {
    return res.status(400).json({
      ok: false,
      message: 'apiBaseUrl ve apiKey zorunludur.',
    });
  }

  runtimeConfig.apiBaseUrl = String(apiBaseUrl).trim().replace(/\/$/, '');
  runtimeConfig.apiKey = String(apiKey).trim();
  runtimeConfig.mailDomain = String(mailDomain || runtimeConfig.mailDomain || 'alfabe.co').trim();

  return res.status(200).json({
    ok: true,
    message: 'Mailcow ayarları sunucu tarafında güncellendi.',
    apiBaseUrl: runtimeConfig.apiBaseUrl,
    mailDomain: runtimeConfig.mailDomain,
  });
});

app.all(/^\/api\/mailcow\/(.+)/, async (req, res) => {
  if (!runtimeConfig.apiBaseUrl || !runtimeConfig.apiKey) {
    return res.status(400).json({
      ok: false,
      message: 'Mailcow yapılandırması eksik. Önce /api/mailcow/config çağrısı yapın.',
    });
  }

  const targetPath = req.params[0];
  const targetUrl = `${runtimeConfig.apiBaseUrl}/${targetPath}`;

  try {
    const response = await fetch(targetUrl, {
      method: req.method,
      headers: {
        'X-API-Key': runtimeConfig.apiKey,
        'Content-Type': 'application/json',
      },
      body: ['GET', 'HEAD'].includes(req.method) ? undefined : JSON.stringify(req.body || {}),
    });

    const raw = await response.text();

    res.status(response.status);
    res.setHeader('Content-Type', response.headers.get('content-type') || 'application/json; charset=utf-8');
    res.send(raw);
  } catch (error) {
    res.status(502).json({
      ok: false,
      message: `Proxy hatası: ${error.message}`,
    });
  }
});

app.listen(port, () => {
  console.log(`Mailcow proxy server çalışıyor: http://localhost:${port}`);
});
