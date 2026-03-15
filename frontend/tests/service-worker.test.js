import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';

test('service worker: tidak mengandung "nextshop" lagi', () => {
  const repoRoot = path.resolve(import.meta.dirname, '..', '..');
  const swPath = path.join(repoRoot, 'public', 'service-worker.js');
  const sw = fs.readFileSync(swPath, 'utf8');

  assert.equal(sw.includes('nextshop'), false);
});

test('service worker: memiliki pola precache & routing (caching + fetch handling)', () => {
  const repoRoot = path.resolve(import.meta.dirname, '..', '..');
  const swPath = path.join(repoRoot, 'public', 'service-worker.js');
  const sw = fs.readFileSync(swPath, 'utf8');

  assert.match(sw, /precacheAndRoute\(/);
  assert.match(sw, /registerRoute\(/);
});

test('registrasi SW: register-service-worker menggunakan SERVICE_WORKER_FILE', () => {
  const registerPath = path.resolve(import.meta.dirname, '..', 'src-pwa', 'register-service-worker.js');
  const content = fs.readFileSync(registerPath, 'utf8');

  assert.match(content, /\bregister\(/);
  assert.match(content, /process\.env\.SERVICE_WORKER_FILE/);
});

