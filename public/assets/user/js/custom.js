/** Shopify CDN: Minification failed

 Line 63:8 Transforming const to the configured target environment ("es5") is not supported yet
 Line 77:4 Transforming const to the configured target environment ("es5") is not supported yet

 **/
var cookies = {
    setCookie: function (name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    },
    getCookie: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    },
    eraseCookie: function (name) {
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
}

/** Country based popup */
var countrySelector = {
    init: function() {
        var websiteCountry = "EU";
        var suggestedSite = 'US';

        // Dev delete cookies for test
        if(window.location.href.indexOf('cookies') > -1) {
            cookies.eraseCookie('multisite');
            cookies.eraseCookie('seen-banner');
            cookies.eraseCookie('preferred-store');
        }

        if(window.location.href.indexOf('swapstore') > -1) {
            cookies.eraseCookie('preferred-store');
        }

        // Does QS have a workaround? If it does - 1 day cookie exception for country
        if(window.location.href.indexOf('multisite') > -1) {
            cookies.setCookie('multisite', 1, 1);
            return;
        }

        // Check for multisite exception
        if(cookies.getCookie('multisite')) {
            return;
        }

        // Check for preferred


        const siteLinks = document.querySelectorAll('.sites');
        siteLinks.forEach(function(siteLink) {
            siteLink.addEventListener('click', function() {
                var country = siteLink.parentElement.dataset.country;
                var domain = siteLink.dataset.domain;

                cookies.setCookie('preferred-store', domain, 28);
                window.location.replace(domain);
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const siteLinks = document.querySelectorAll('.sites');
    siteLinks.forEach(function(siteLink) {
        siteLink.addEventListener('click', function() {
            cookies.setCookie('preferred-store', siteLink.dataset.domain, 28);
            return true;
        });
    });

    countrySelector.init();
});
