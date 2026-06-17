// Минимальный статический сервер для продакшн-сборки (гл.6, локальный аналог Heroku static).
// Отдаёт frontend/dist на :5000, для неизвестных путей — SPA-fallback на index.html.
const http = require('http');
const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, 'dist');
const PORT = 5000;
const MIME = {
  '.html': 'text/html; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png', '.jpg': 'image/jpeg', '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon', '.woff': 'font/woff', '.woff2': 'font/woff2',
  '.ttf': 'font/ttf', '.eot': 'application/vnd.ms-fontobject',
};

http.createServer((req, res) => {
  const urlPath = decodeURIComponent(req.url.split('?')[0]);
  let filePath = path.join(ROOT, urlPath);
  if (!filePath.startsWith(ROOT)) { res.writeHead(403); return res.end(); }

  fs.stat(filePath, (err, st) => {
    if (!err && st.isFile()) return send(filePath);
    // SPA-fallback: всё неизвестное -> index.html (history-режим vue-router)
    return send(path.join(ROOT, 'index.html'));
  });

  function send(fp) {
    fs.readFile(fp, (e, buf) => {
      if (e) { res.writeHead(404); return res.end('Not found'); }
      res.writeHead(200, { 'Content-Type': MIME[path.extname(fp)] || 'application/octet-stream' });
      res.end(buf);
    });
  }
}).listen(PORT, () => console.log('prod frontend on http://localhost:' + PORT));
