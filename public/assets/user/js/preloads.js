
(function() {
    var baseURL = "https://cdn.shopify.com/shopifycloud/checkout-web/assets/";
    var scripts = ["https://cdn.shopify.com/shopifycloud/checkout-web/assets/runtime.latest.en.6132fd48f004b69ec13e.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/172.latest.en.d9a2fc217cd7afd5090b.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/593.latest.en.611d72b7c673aaf35435.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/150.latest.en.245c984c955a95db4eeb.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/app.latest.en.5cc7f4162cc4a6af2545.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/731.latest.en.13d4de92b88330e8fea9.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/958.latest.en.7ab533e6ba2a828e441d.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/844.latest.en.7fcd45ae446a9a5574e8.js","https://cdn.shopify.com/shopifycloud/checkout-web/assets/OnePage.latest.en.371c65903442f75b19c3.js"];
    var styles = ["https://cdn.shopify.com/shopifycloud/checkout-web/assets/172.latest.en.041723f154cf114fb9c6.css","https://cdn.shopify.com/shopifycloud/checkout-web/assets/app.latest.en.e5a7f63ca146c0549466.css","https://cdn.shopify.com/shopifycloud/checkout-web/assets/958.latest.en.3388a58cacfe5a93e981.css","https://cdn.shopify.com/shopifycloud/checkout-web/assets/74.latest.en.c6fc9403a4c873030d42.css"];
    var fontPreconnectUrls = [];
    var fontPrefetchUrls = [];
    var imgPrefetchUrls = ["https://cdn.shopify.com/s/files/1/0587/5816/8785/files/Represent_Logo_2d90c344-d370-4b81-85e9-77b4817166ce_x320.png?v=1675968323"];

    function preconnect(url, callback) {
        var link = document.createElement('link');
        link.rel = 'dns-prefetch preconnect';
        link.href = url;
        link.crossOrigin = '';
        link.onload = link.onerror = callback;
        document.head.appendChild(link);
    }

    function preconnectAssets() {
        var resources = [baseURL].concat(fontPreconnectUrls);
        var index = 0;
        (function next() {
            var res = resources[index++];
            if (res) preconnect(res[0], next);
        })();
    }

    function prefetch(url, as, callback) {
        var link = document.createElement('link');
        if (link.relList.supports('prefetch')) {
            link.rel = 'prefetch';
            link.fetchPriority = 'low';
            link.as = as;
            if (as === 'font') link.type = 'font/woff2';
            link.href = url;
            link.crossOrigin = '';
            link.onload = link.onerror = callback;
            document.head.appendChild(link);
        } else {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onloadend = callback;
            xhr.send();
        }
    }

    function prefetchAssets() {
        var resources = [].concat(
            scripts.map(function(url) { return [url, 'script']; }),
            styles.map(function(url) { return [url, 'style']; }),
            fontPrefetchUrls.map(function(url) { return [url, 'font']; }),
            imgPrefetchUrls.map(function(url) { return [url, 'image']; })
        );
        var index = 0;
        (function next() {
            var res = resources[index++];
            if (res) prefetch(res[0], res[1], next);
        })();
    }

    function onLoaded() {
        preconnectAssets();
        prefetchAssets();
    }

    if (document.readyState === 'complete') {
        onLoaded();
    } else {
        addEventListener('load', onLoaded);
    }
})();

