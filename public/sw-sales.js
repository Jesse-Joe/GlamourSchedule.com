/**
 * GlamourSchedule Sales Portal Service Worker
 * Handles offline caching for the sales dashboard
 */

const CACHE_NAME = 'gs-sales-v3';
const OFFLINE_URL = '/sales/offline';

const STATIC_ASSETS = [
    '/sales/dashboard',
    '/sales/materials',
    '/sales/referrals',
    '/manifest-sales.json',
    '/images/sales-icon-192.png',
    '/images/sales-icon-512.png',
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
                    .filter(name => name.startsWith('gs-sales-') && name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - network first with cache fallback
self.addEventListener('fetch', event => {
    const { request } = event;

    if (request.method !== 'GET') return;

    // Skip API requests
    if (request.url.includes('/api/') || request.url.includes('/sales/send-')) return;

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

// Push notifications for sales updates
self.addEventListener('push', event => {
    let data = {
        title: 'GlamourSchedule Sales',
        body: 'Je hebt een nieuwe referral!',
        icon: '/images/sales-icon-192.png',
        badge: '/images/sales-icon-192.png',
        data: { url: '/sales/dashboard' }
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

    const url = event.notification.data?.url || '/sales/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes('/sales/') && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
