// Minimal service worker for PWA installability (Chrome/Edge).
const CACHE_NAME = 'cpsuvotewisely-v1';
self.addEventListener('install', function (event) {
    self.skipWaiting();
});
self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});
