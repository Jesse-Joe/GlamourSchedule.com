/**
 * GlamourSchedule Business Portal Service Worker
 * Handles offline caching for the business dashboard
 */

const CACHE_NAME = 'gs-business-v1';

const STATIC_ASSETS = [
    '/business/dashboard',
    '/business/calendar',
    '/business/bookings',
    '/manifest-business.json',
    '/icon-192.png',
    '/icon-512.png',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// Install event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name.startsWith('gs-business-') && name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - network first for real-time data
self.addEventListener('fetch', event => {
    const { request } = event;

    if (request.method !== 'GET') return;

    // Skip API requests - always go to network
    if (request.url.includes('/api/')) return;

    event.respondWith(
        fetch(request)
            .then(response => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request))
    );
});

// Push notifications for new bookings
self.addEventListener('push', event => {
    let data = {
        title: 'GlamourSchedule',
        body: 'Je hebt een nieuwe boeking!',
        icon: '/icon-192.png',
        badge: '/icon-192.png',
        data: { url: '/business/bookings' }
    };

    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            vibrate: [200, 100, 200],
            data: data.data,
            requireInteraction: true,
            actions: [
                { action: 'view', title: 'Bekijken' },
                { action: 'dismiss', title: 'Sluiten' }
            ]
        })
    );
});

// Notification click
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const url = event.notification.data?.url || '/business/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes('/business/') && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
