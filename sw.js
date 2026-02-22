self.addEventListener('push', function(event) {
    const data = event.data.json();

    self.registration.showNotification(data.title, {
        body: data.body,
        icon: '/logo.png',
        badge: '/logo.png'
    });
});

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    clients.claim();
});