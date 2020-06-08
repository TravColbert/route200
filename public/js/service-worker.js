var CACHE_NAME = 'phpht-sw-cache-v1'
var dataToCache = [
  '/',
  '/demo/',
  '/demo/json/',
  '/public/css/phpht.css',
  '/public/img/favicons/android-icon-192x192.png',
  '/public/img/favicons/favicon-32x32.png',
  '/public/img/phpht.png',
  '/public/js/pwa_demo.js'
]

self.addEventListener('install', (event) => {
  console.log('Install')
  // Initialize cache
  event.waitUntil(
    caches.open(CACHE_NAME)
    .then((cache) => {
      console.log('Opened cache')
      return cache.addAll(dataToCache)
    })
  )
})

self.addEventListener('activate', (event) => {
  console.log('Activate!')
})

self.addEventListener('fetch', (event) => {
  requestKeys = Object.keys(event.request)
  console.log(`Fetching: ${event.request}`)
  event.respondWith(
    caches.match(event.request)
    .then(response => {
      if(response) {
        console.log(`Found match in cache for: ${event.request}`)
        return response
      }

      console.log(`No cache match found - fetching from network`)
      return fetch(event.request)
      .then(response => {
        // If something's not right about the response pass it down but don't 
        // put it in the cache
        if(!response || response.status !== 200 || response.type !== 'basic') {
          return response
        }

        // Otherwise, make a copy of the response for putting in the cache
        let responseToCache = response.clone();

        caches.open(CACHE_NAME)
        .then(cache => {
          console.log(`Inserting data in cache slot: ${event.request}`)
          cache.put(event.request, responseToCache)
        })

        return response
      })
      .catch(() => {
        return new Response("Nothing found there.")
      })
    })
  )
})