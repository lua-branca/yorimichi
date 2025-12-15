// Google Analytics (GA4) Implementation
// ID: G-T56QMT2RYC

(function () {
    // Inject the Gtag script from Google
    const script = document.createElement('script');
    script.src = 'https://www.googletagmanager.com/gtag/js?id=G-T56QMT2RYC';
    script.async = true;
    document.head.appendChild(script);

    // Initialize dataLayer and gtag function
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    // Config with Measurement ID
    gtag('config', 'G-T56QMT2RYC');

    // Custom Analytics Tracking
    window.addEventListener('load', function () {
        try {
            const data = new FormData();
            data.append('url', window.location.href);
            data.append('referrer', document.referrer);

            // Use sendBeacon if available for reliable background sending
            if (navigator.sendBeacon) {
                navigator.sendBeacon('/php/track.php', data);
            } else {
                // Fallback
                fetch('/php/track.php', {
                    method: 'POST',
                    body: data
                });
            }
        } catch (e) {
            console.error('Tracking Error', e);
        }
    });

})();
