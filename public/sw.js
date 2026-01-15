/**
 * GlamourSchedule Service Worker
 * Handles push notifications and offline caching
 */

const CACHE_NAME = 'glamourschedule-v3';
const STATIC_ASSETS = [
    '/',
    '/manifest.json'
];

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
                    .filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Skip API requests
    if (event.request.url.includes('/api/')) return;

    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
            .catch(() => {
                // Return offline page if available
                if (event.request.mode === 'navigate') {
                    return caches.match('/');
                }
            })
    );
});

// Push notification event
self.addEventListener('push', event => {
    console.log('Push notification received', event);

    let data = {
        title: 'GlamourSchedule',
        body: 'Je hebt een nieuw bericht',
        icon: '/images/icon-192.png',
        badge: '/images/badge-72.png',
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
        icon: data.icon || '/images/icon-192.png',
        badge: data.badge || '/images/badge-72.png',
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
    console.log('Notification clicked', event);

    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/';

    // Handle action buttons
    if (event.action) {
        switch (event.action) {
            case 'view':
                // Open the URL
                break;
            case 'dismiss':
                return;
        }
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Check if there's already a window open
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.navigate(urlToOpen);
                        return client.focus();
                    }
                }
                // Open a new window
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
    // Sync any pending bookings when back online
    console.log('Syncing bookings...');
}
