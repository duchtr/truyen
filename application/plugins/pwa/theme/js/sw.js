importScripts("https://storage.googleapis.com/workbox-cdn/releases/4.0.0/workbox-sw.js");
if (workbox) {
    console.log("Yay! Workbox is loaded !");
    const articleHandler = workbox.strategies.networkFirst({
      cacheName: 'articles-cache',
      plugins: [
        new workbox.expiration.Plugin({
          maxEntries: 50,
        })
      ]
    });
    const articleHandler1 = new workbox.strategies.StaleWhileRevalidate({
            cacheName: "articles-cache1",
            plugins:[
                new workbox.expiration.Plugin({
                    maxAgeSeconds: 60 * 60 * 24 * 30,
                    maxEntries: 30,
                    purgeOnQuotaError: true
                })
            ]
        });

    self.addEventListener('install', event => {
      console.log('Service worker install event!');
    });
    self.addEventListener('activate', event => {
      console.log('Service worker activate event!');
    });
    workbox.precaching.precacheAndRoute(['offline']);
    workbox.routing.registerRoute(
        /.*\.(?:png|gif|jpg|webp|svg)/,
        new workbox.strategies.CacheFirst({
            cacheName: "images",
            plugins: [
                new workbox.expiration.Plugin({
                    maxEntries: 50,
                    maxAgeSeconds: 30 * 24 * 60 * 60,
                    purgeOnQuotaError: true
                })
            ]
        })
    );
    workbox.routing.registerRoute(
        /theme\/.*\.(?:css|js|scss|)/,
        new workbox.strategies.StaleWhileRevalidate({
            cacheName: "assets",
            plugins:[
                new workbox.expiration.Plugin({
                    maxAgeSeconds: 60 * 60 * 24 * 30,
                    maxEntries: 30,
                    purgeOnQuotaError: true
                })
            ]
        })
    );
    workbox.routing.registerRoute(
        new RegExp("https://(fonts|storage).(?:googleapis|gstatic).com/(.*)"),
        new workbox.strategies.CacheFirst({
            cacheName: "google-fonts",
            plugins: [
                new workbox.cacheableResponse.Plugin({
                    statuses: [0, 200],
                }),
                new workbox.expiration.Plugin({
                    maxAgeSeconds: 60 * 60 * 24 * 30,
                    maxEntries: 30,
                    purgeOnQuotaError: true
                })
            ],
        })
    );
    workbox.routing.registerRoute(/^((?!(Techsystem|techsystem)).)*$/, args => {
      return articleHandler1.handle(args).then(response => {
        if (!response) {
          return caches.match('offline');
        }
        return response;
      }).catch(error=>{
        return caches.match('offline');
      })
      ;
    });

    workbox.googleAnalytics.initialize();
    workbox.core.skipWaiting();
    workbox.core.clientsClaim();
} else {
    console.log("Oops! Workbox didn't load");
}