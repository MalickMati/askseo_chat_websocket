<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ASKSEO | Chat App')</title>
    <link rel="stylesheet" href="{{ secure_asset('css/style.css') }}">
    <link rel="shortcut icon" href="{{ secure_asset('favicon.ico') }}" type="image/x-icon">
    <meta name="theme-color" content="#6526DE">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">

    <!-- Apple-specific for iOS -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ASK SEO CHAT APP">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ secure_asset('/assets/images/icon-192.png') }}">
    <script src="{{ secure_asset('js/image_secure.js') }}"></script>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        function b64ToU8(b64) {
            const p = '='.repeat((4 - b64.length % 4) % 4);
            const s = (b64 + p).replace(/-/g, '+').replace(/_/g, '/');
            const r = atob(s);
            return Uint8Array.from([...r].map(c => c.charCodeAt(0)));
        }

        async function hashEndpoint(endpoint, vapid) {
            const data = new TextEncoder().encode(endpoint + '|' + vapid);
            const buf = await crypto.subtle.digest('SHA-256', data);
            return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2, '0')).join('');
        }

        async function enablePush() {
            try {
                if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

                const perm = await Notification.requestPermission();
                if (perm !== 'granted') return;

                const reg = await navigator.serviceWorker.ready;

                let sub = await reg.pushManager.getSubscription();
                if (!sub) {
                    const vapid = document.querySelector('meta[name="vapid-public-key"]').content;
                    sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: b64ToU8(vapid)
                    });
                }

                // Send only once per endpoint+vapid combo
                const vapid = document.querySelector('meta[name="vapid-public-key"]').content;
                const currentHash = await hashEndpoint(sub.endpoint, vapid);
                const savedHash = localStorage.getItem('push:endpointHash');

                if (savedHash === currentHash) {
                    console.log('Subscription already saved. Skipping POST.');
                    return;
                }

                const res = await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(sub.toJSON())
                });

                if (!res.ok) {
                    const txt = await res.text().catch(() => '');
                    console.error('Subscribe failed', res.status, txt);
                    return;
                }

                localStorage.setItem('push:endpointHash', currentHash);
                console.log('Push enabled and saved once.');
            } catch (err) {
                console.error('enablePush error', err);
            }
        }

        // Trigger once on first interaction
        window.addEventListener('click', () => enablePush(), { once: true });
        window.addEventListener('keydown', () => enablePush(), { once: true });
    </script>


</head>

<body>
    @yield('content')

    <script>
        const pollingtime = 2000;
    </script>
    <script>
        const vapidPublicKey = "{{ env('VAPID_PUBLIC_KEY') }}";
        // if ('serviceWorker' in navigator && 'PushManager' in window) {
        //     navigator.serviceWorker.register('/service-worker.js')
        //         .then(swReg => {
        //             window.swRegistration = swReg;
        //         })
        //         .catch(err => console.error('Service Worker registration failed', err));
        // }
    </script>
    <script>
        const socket = io('https://socket.askseo.me/');

        socket.on('connect', () => {
            console.log('Connected to Server');
        });

        socket.on('disconnect', () => {
            //
        });
        socket.on('json_data', (data) => {
            if (data.key === 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
                && data.channel === 'group'
                && data.data.chatapp_data.receivers.includes(currentUserId)) {
                if (activeReceiverId) {
                    loadMessages(activeReceiverId, false);
                } else if (activeGroupId) {
                    loadMessages(activeGroupId, true);
                }
                refreshSidebar();
            }
        });

        socket.on('json_data', (data) => {
            if (data.key === 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
                && data.channel === 'admin_notification'
                && data.data.chatapp_data.admins.includes(currentUserId)) {
                showNotificationToast(5, data.data.chatapp_data.message, 3000);
                messageSound.play().catch(e => console.warn("Sound play failed:", e));
            }
        });
    </script>
    @yield('js')
</body>

</html>