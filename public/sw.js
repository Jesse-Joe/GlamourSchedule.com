/**
 * GlamourSchedule Service Worker
 * Handles push notifications, offline caching, and background sync
 */

const CACHE_NAME = 'glamourschedule-v4';
const OFFLINE_URL = '/offline.html';

const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/css/prestige.css',
    '/icon-192.png',
    '/icon-512.png',
    '/apple-touch-icon.png',
    '/images/gs-logo-circle.svg',
    OFFLINE_URL
];

const CACHE_STRATEGIES = {
    cacheFirst: ['image', 'font', 'style'],
    networkFirst: ['document', 'script'],
    staleWhileRevalidate: []
};

// Install event - cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name.startsWith('glamourschedule-') && name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache with network fallback
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip API requests (always network)
    if (url.pathname.startsWith('/api/')) return;

    // Skip external requests
    if (url.origin !== self.location.origin) return;

    // Handle navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Cache-first for static assets
    if (isStaticAsset(url.pathname)) {
        event.respondWith(
            caches.match(request)
                .then(cached => {
                    if (cached) return cached;
                    return fetch(request).then(response => {
                        if (response.ok) {
                            const clone = response.clone();
                            caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                        }
                        return response;
                    });
                })
        );
        return;
    }

    // Network-first for everything else
    event.respondWith(
        fetch(request)
            .then(response => {
                if (response.ok && request.url.includes(self.location.origin)) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request))
    );
});

function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/.test(pathname);
}

// Push notification event
self.addEventListener('push', event => {
    console.log('Push notification received', event);

    let data = {
        title: 'GlamourSchedule',
        body: 'Je hebt een nieuw bericht',
        icon: '/icon-192.png',
        badge: '/favicon-32.png',
        tag: 'glamourschedule-notification',
        requireInteraction: false,
        data: {
            url: '/'
        }
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/icon-192.png',
        badge: data.badge || '/favicon-32.png',
        tag: data.tag || 'glamourschedule-notification',
        requireInteraction: data.requireInteraction || false,
        vibrate: [200, 100, 200],
        data: data.data || { url: '/' },
        actions: data.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/';

    if (event.action === 'dismiss') return;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.navigate(urlToOpen);
                        return client.focus();
                    }
                }
                return clients.openWindow(urlToOpen);
            })
    );
});

// Background sync for offline actions
self.addEventListener('sync', event => {
    if (event.tag === 'sync-bookings') {
        event.waitUntil(syncBookings());
    }
});

async function syncBookings() {
    console.log('Syncing bookings...');
    // Sync any pending bookings when back online
}

// Periodic background sync
self.addEventListener('periodicsync', event => {
    if (event.tag === 'update-content') {
        event.waitUntil(updateContent());
    }
});

async function updateContent() {
    // Refresh cached content periodically
    const cache = await caches.open(CACHE_NAME);
    await cache.add('/');
}
