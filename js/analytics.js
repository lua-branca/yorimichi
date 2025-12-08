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
})();
