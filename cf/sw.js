// Oshi Tier Maker — Service Worker (offline support for the static cf/ version)
// Bump VERSION together with the app version (see CLAUDE.md Version Bump Checklist)
'use strict';

const VERSION = '0.11.0';
const CACHE_PREFIX = 'oshi-tier-';
const CACHE = CACHE_PREFIX + VERSION;
const NAV_TIMEOUT_MS = 3000;

// App shell is cached under './' only — hosts like Cloudflare Pages redirect /index.html → /,
// and a redirected response can't be used to answer a navigation
const SHELL = './';
const PRECACHE = [
    SHELL,
    './manifest.webmanifest',
    './vendor/jszip.min.js',
    './icons/favicon-48.png',
    './icons/icon-192.png',
    './icons/icon-512.png',
    './icons/icon-maskable-512.png',
    './icons/apple-touch-icon.png',
];

// No skipWaiting() here — a new worker waits until the page asks for it (update bar)
self.addEventListener('install', e => {
    e.waitUntil(caches.open(CACHE).then(c => c.addAll(PRECACHE.map(u => new Request(u, { cache: 'reload' })))));
});

self.addEventListener('activate', e => {
    e.waitUntil((async () => {
        const keys = await caches.keys();
        await Promise.all(keys.filter(k => k.startsWith(CACHE_PREFIX) && k !== CACHE).map(k => caches.delete(k)));
        await self.clients.claim();
    })());
});

self.addEventListener('message', e => {
    if (e.data?.type === 'SKIP_WAITING') self.skipWaiting();
});

self.addEventListener('fetch', e => {
    const req = e.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;

    if (req.mode === 'navigate') e.respondWith(networkFirst(req));
    else e.respondWith(cacheFirst(req));
});

// HTML: always try the network so updates arrive; fall back to the cached shell when offline/slow
async function networkFirst(req) {
    const cache = await caches.open(CACHE);
    try {
        const res = await withTimeout(fetch(req), NAV_TIMEOUT_MS);
        const isHtml = (res.headers.get('content-type') || '').includes('text/html');
        if (res.ok && isHtml && !res.redirected) cache.put(SHELL, res.clone());
        return res;
    } catch {
        return (await cache.match(SHELL)) || Response.error();
    }
}

async function cacheFirst(req) {
    const cache = await caches.open(CACHE);
    const hit = await cache.match(req, { ignoreSearch: true });
    if (hit) return hit;
    const res = await fetch(req);
    if (res.ok) cache.put(req, res.clone());
    return res;
}

function withTimeout(promise, ms) {
    return new Promise((resolve, reject) => {
        const timer = setTimeout(() => reject(new Error('timeout')), ms);
        promise.then(v => { clearTimeout(timer); resolve(v); }, err => { clearTimeout(timer); reject(err); });
    });
}
