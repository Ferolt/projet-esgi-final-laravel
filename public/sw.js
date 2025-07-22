const CACHE_NAME = 'kanboard-v1.0.0';
const STATIC_CACHE = 'kanboard-static-v1.0.0';
const DYNAMIC_CACHE = 'kanboard-dynamic-v1.0.0';

const STATIC_FILES = [
    '/',
    '/workspaces',
    '/recent',
    '/offline',
    '/manifest.json',
    '/apple-touch-icon.png',
    '/favicon-16x16.png',
    '/favicon-32x32.png',
    '/offline-styles.css',
    'https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    '/favicon.ico'
];

const EXCLUDE_URLS = [
    '/api/',
    '/admin/',
    '/login',
    '/register',
    '/password/',
    '/email/',
    'chrome-extension://',
    'moz-extension://',
    '127.0.0.1:5174',  // Vite dev server
    'localhost:5173'   // Vite dev server
];

self.addEventListener('install', event => {
    console.log('SW: Installation en cours...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('SW: Mise en cache des fichiers statiques');
                return cache.addAll(STATIC_FILES.map(url => {
                    return new Request(url, {
                        cache: 'no-cache'
                    });
                }));
            })
            .then(() => {
                console.log('SW: Installation terminée');
                self.skipWaiting();
            })
            .catch(error => {
                console.error('SW: Erreur lors de l\'installation:', error);
            })
    );
});

self.addEventListener('activate', event => {
    console.log('SW: Activation en cours...');
    event.waitUntil(
        Promise.all([
            // Supprimer les anciens caches
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME &&
                            cacheName !== STATIC_CACHE &&
                            cacheName !== DYNAMIC_CACHE) {
                            console.log('SW: Suppression du cache obsolète:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
        ])
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    if (EXCLUDE_URLS.some(excludeUrl =>
        url.pathname.includes(excludeUrl) ||
        url.href.includes(excludeUrl)
    )) {
        return;
    }

    if (request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'manifest' ||
        url.pathname.includes('/build/') ||
        url.pathname.includes('.css') ||
        url.pathname.includes('.js') ||
        url.pathname.includes('.png') ||
        url.pathname.includes('.jpg') ||
        url.pathname.includes('.svg')) {
        event.respondWith(cacheFirst(request));
        return;
    }

    if (request.destination === 'document' ||
        (request.method === 'GET' && request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
        event.respondWith(networkFirstWithFallback(request));
        return;
    }

    if (url.pathname.includes('/api/') && request.method === 'GET') {
        event.respondWith(networkFirst(request));
        return;
    }
});

async function cacheFirst(request) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        // Si c'est une ressource Vite en développement et qu'on est hors ligne, ignorer
        const url = new URL(request.url);
        if (url.hostname === '127.0.0.1' || url.hostname === 'localhost') {
            if (url.port === '5174' || url.port === '5173') {
                return new Response('', { status: 204 }); // No content
            }
        }

        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.warn('SW: Cache first failed for:', request.url, error.message);

        const url = new URL(request.url);
        if (url.hostname === '127.0.0.1' || url.hostname === 'localhost') {
            if (url.port === '5174' || url.port === '5173') {
                return new Response('', { status: 204 });
            }
        }

        return new Response('Contenu non disponible hors ligne', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.warn('SW: Network first failed:', error.message);
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        return new Response('Contenu non disponible hors ligne', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

async function networkFirstWithFallback(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.warn('SW: Network first with fallback failed:', error.message);

        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        if (request.destination === 'document') {
            return caches.match('/offline');
        }

        return new Response('Contenu non disponible hors ligne', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

self.addEventListener('sync', event => {
    console.log('SW: Synchronisation en arrière-plan:', event.tag);

    if (event.tag === 'sync-tasks') {
        event.waitUntil(syncTasks());
    }
});

async function syncTasks() {
    try {
        console.log('SW: Synchronisation des tâches terminée');
    } catch (error) {
        console.error('SW: Erreur lors de la synchronisation:', error);
    }
}

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});
