const http = require('http');

const TARGET_PORT = 8080;
const LISTEN_PORT = process.env.PORT || 9191;

const server = http.createServer((req, res) => {
  const proxyReq = http.request(
    {
      host: 'localhost',
      port: TARGET_PORT,
      path: req.url,
      method: req.method,
      headers: req.headers,
    },
    (proxyRes) => {
      res.writeHead(proxyRes.statusCode, proxyRes.headers);
      proxyRes.pipe(res, { end: true });
    }
  );

  req.pipe(proxyReq, { end: true });

  proxyReq.on('error', (err) => {
    res.writeHead(502);
    res.end('Proxy error: ' + err.message);
  });
});

server.on('upgrade', (req, socket, head) => {
  const proxyReq = http.request({
    host: 'localhost',
    port: TARGET_PORT,
    path: req.url,
    method: req.method,
    headers: req.headers,
  });

  proxyReq.end();

  proxyReq.on('response', () => {});
  proxyReq.on('upgrade', (proxyRes, proxySocket, proxyHead) => {
    socket.write(
      `HTTP/1.1 101 Switching Protocols\r\n` +
        Object.entries(proxyRes.headers)
          .map(([k, v]) => `${k}: ${v}`)
          .join('\r\n') +
        '\r\n\r\n'
    );
    proxySocket.pipe(socket);
    socket.pipe(proxySocket);
  });
});

server.listen(LISTEN_PORT, () => {
  console.log(`Preview proxy listening on ${LISTEN_PORT}, forwarding to localhost:${TARGET_PORT}`);
});
