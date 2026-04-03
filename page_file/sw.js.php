<?php

header('Content-Type: text/javascript; charset=utf-8');


/*
const static_cache_name = '<?php echo($WEB_JSON['page_json']['site_name_short']); ?>-v1';
const dynamic_cache_name = '<?php echo($WEB_JSON['page_json']['site_name_short']); ?>-dynamic-v1';
const asset_urls = []; 

self.addEventListener("install", event => {
	event.waitUntil(
		caches.open(static_cache_name).then(cache => {
			cache.addAll(asset_urls)
		})
	)
})

self.addEventListener('activate', async event => {
	const cacheNames = await caches.keys()
	await Promise.all(
		cacheNames
			.filter(name => name !== static_cache_name)
			.map(name => caches.delete(name))
    )
})

self.addEventListener('fetch', function(event){
	event.respondWith(async function () {
		var cache = await caches.open('cache');
		var cachedResponsePromise = await cache.match(event.request);
		var networkResponsePromise = fetch(event.request);
		event.waitUntil(async function () {
			var networkResponse = await networkResponsePromise;
			await cache.put(event.request, networkResponse.clone());
		}());
		return cachedResponsePromise || networkResponsePromise;
	}());
});
*/


?>

const CACHE_NAME = '<?php echo($WEB_JSON['page_json']['site_name_short']); ?>-v2';


self.addEventListener('install', (event) => {
	self.skipWaiting();
});

self.addEventListener('activate', (event) => {
	event.waitUntil(
		clients.claim()
	);
});
