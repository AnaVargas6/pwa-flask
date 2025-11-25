const CACHE_NAME = 'trajes-cache-v1';

// Archivos que se guardan para que la app funcione sin internet
const APP_SHELL = [
  './',
  './index.html',
  './dashboard.html',
  './manifest.json',
  './icon-192.png',
  './icon-512.png'
];


// INSTALACIÓN: se guarda el "shell" de la app
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(APP_SHELL);
    })
  );
  self.skipWaiting();
});

// ACTIVATE: limpiar cachés viejas si cambias la versión
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    )
  );
  self.clients.claim();
});

// FETCH: estrategia cache-primero para el shell
self.addEventListener('fetch', (event) => {
  const req = event.request;

  // Solo manejamos GET
  if (req.method !== 'GET') return;

  // Para llamadas a backend (PHP), dejamos que las maneje la red
  // y si no hay red, tu JS ya usa localStorage.
  if (req.url.includes('/backend/')) {
    return;
  }

  // Para HTML, CSS, JS, etc.: cache first con fallback a red
  event.respondWith(
    caches.match(req).then((cachedRes) => {
      if (cachedRes) {
        return cachedRes;
      }
      return fetch(req)
        .then((networkRes) => {
          // Guardar en caché lo nuevo que se pida
          return caches.open(CACHE_NAME).then((cache) => {
            cache.put(req, networkRes.clone());
            return networkRes;
          });
        })
        .catch(() => {
          // Si falla todo y es una navegación, al menos devolvemos el dashboard
          if (req.mode === 'navigate') {
            return caches.match('./dashboard.html');
          }
        });
    })
  );
});
