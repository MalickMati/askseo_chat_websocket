<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication | ASKSEO')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ secure_asset('css/auth.css') }}">
    <link rel="shortcut icon" href="{{ secure_asset('favicon.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">
    <meta name="theme-color" content="#6526DE">

    <!-- Apple-specific for iOS -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ASK SEO CHAT APP">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ secure_asset('/assets/images/icon-192.png') }}">


    <!-- Example button somewhere in your HTML -->
</head>

<body>


    <div class="auth-container holographic">
        @yield('form')
    </div>

    @yield('js_file')
    <script src="{{ secure_asset('sw.js') }}"></script>
    <script>
        let deferredPrompt = null;

        // Catch the install event but don't show the browser UI yet
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // Optional: show your button as a fallback
            const btn = document.getElementById('installPWA');
            if (btn) btn.style.display = 'block';
        });

        // If already installed, hide any UI
        window.addEventListener('load', () => {
            const installed =
                window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone === true;
            if (installed) {
                const btn = document.getElementById('installPWA');
                if (btn) btn.style.display = 'none';
            }
        });

        async function tryInstall() {
            if (deferredPrompt) {
                const ok = window.confirm('Install ASK SEO CHAT APP on this device?');
                if (!ok) return;
                deferredPrompt.prompt();
                await deferredPrompt.userChoice; // 'accepted' or 'dismissed'
                deferredPrompt = null;
                const btn = document.getElementById('installPWA');
                if (btn) btn.style.display = 'none';
                return;
            }

            // Fallback for iOS which has no beforeinstallprompt
            const isiOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
            const standalone =
                window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone === true;
            if (isiOS && !standalone) {
                alert('On iPhone, open in Safari, tap Share, then Add to Home Screen.');
            }
        }

        // Auto trigger on first user interaction anywhere
        window.addEventListener('click', () => { tryInstall(); }, { once: true });
        window.addEventListener('keydown', () => { tryInstall(); }, { once: true });

        // Keep your button as a backup entry point
        window.installPWA = tryInstall;

        // Hide UI once installed
        window.addEventListener('appinstalled', () => {
            const btn = document.getElementById('installPWA');
            if (btn) btn.style.display = 'none';
            console.log('PWA installed');
        });

        // Keep your service worker registration as is
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js?v=4'); // bump the query
            });
        }
        self.addEventListener('fetch', (event) => {
            if (event.request.mode === 'navigate') {
                event.respondWith(fetch(event.request).catch(() => new Response('Offline', { status: 200 })));
            }
        });
    </script>
</body>

</html>