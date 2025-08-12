<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ secure_asset('css/admin.css') }}">
    <link rel="shortcut icon" href="{{ secure_asset('favicon.ico') }}" type="image/x-icon">
    <meta name="theme-color" content="#6526DE">
    <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">

    <!-- Apple-specific for iOS -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ASK SEO CHAT APP">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ secure_asset('/assets/images/icon-192.png') }}">
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    @yield('css')
</head>

<body>
    <div class="admin-container">
        @yield('main_content')

    </div>
    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>



    <script>
        function showNotificationToast(code = 1, message = "Success", duration = 2000) {
            const toast = document.getElementById('notification-toast');
            const messageSpan = document.getElementById('notification-message');

            // Clear previous classes
            toast.classList.remove('notification-success', 'notification-warning', 'notification-error', 'notification-smsmessage');

            // Assign new class based on code
            switch (code) {
                case 1:
                    toast.classList.add('notification-success');
                    messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16m3.293 4.293L10 13.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l6-6a1 1 0 1 0-1.414-1.414"/></svg>' + message;
                    break;
                case 2:
                    toast.classList.add('notification-warning');
                    messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.56 1h.88l6.54 12.26-.44.74H1.44L1 13.26zM8 2.28 2.28 13H13.7zM8.625 12v-1h-1.25v1zm-1.25-2V6h1.25v4z"/></svg>' + message;
                    break;
                case 3:
                    toast.classList.add('notification-error');
                    messageSpan.innerHTML = '<svg width="20" height="20" viewBox="0 0 52 52" fill="#fff" xml:space="preserve"><path d="M26 2C12.8 2 2 12.8 2 26s10.8 24 24 24 24-10.8 24-24S39.2 2 26 2M8 26c0-9.9 8.1-18 18-18 3.9 0 7.5 1.2 10.4 3.3L11.3 36.4C9.2 33.5 8 29.9 8 26m18 18c-3.9 0-7.5-1.2-10.4-3.3l25.1-25.1C42.8 18.5 44 22.1 44 26c0 9.9-8.1 18-18 18"/></svg>' + message;
                    break;
                case 4:
                    toast.classList.add('notification-smsmessage');
                    messageSpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" fill="#fff" height="20" viewBox="0 0 30 26" xml:space="preserve"><path d="M28.738 25.208c-1.73-.311-3.77-1.471-4.743-3.621C27.635 19.396 30 15.923 30 12c0-6.627-6.716-12-15-12S0 5.373 0 12s6.716 12 15 12c1.111 0 2.191-.104 3.232-.287 2.86 1.975 6.252 2.609 10.41 2.139.248-.02.356-.148.356-.326a.32.32 0 0 0-.26-.318M9 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4m6 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4m6 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4"/><g/></svg>' + message;
                    break;
                case 5:
                    toast.classList.add('notification-smsmessage');
                    messageSpan.innerHTML = '<svg width="20" height="20" viewBox="-2 0 34 34" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 0a3.5 3.5 0 0 1 3.48 3.124 7 7 0 0 0 7.021 11.586L26 23h.5a3.5 3.5 0 1 1 0 7h-5.842a6.002 6.002 0 0 1-11.316 0H3.5a3.5 3.5 0 1 1 0-7H4v-9c0-4.664 2.903-8.65 7-10.25V3.5A3.5 3.5 0 0 1 14.308.005L14.5 0zM24 3a5 5 0 1 1 0 10 5 5 0 0 1 0-10m-8.5-1h-1a1.5 1.5 0 0 0-1.465 1.175 11.1 11.1 0 0 1 3.928 0A1.5 1.5 0 0 0 15.5 2" fill="#fff"/></svg>' + message;
                    break;
                default:
                    toast.classList.add('notification-success');
                    messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16m3.293 4.293L10 13.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l6-6a1 1 0 1 0-1.414-1.414"/></svg>' + message;
                    break;
            }

            toast.classList.add('show');
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.classList.add('hidden'), 400);
            }, duration);
        }
        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('light-theme');
            }
            window.userId = {{ auth()->id() }};
        });
    </script>

    <script>
        const socket = io('https://socket.askseo.me/');
        const messageSound = new Audio('/sounds/sound.mp3');

        socket.on('connect', () => {
            //
        });

        socket.on('disconnect', () => {
            //
        });

        socket.on('json_data', (data) => {
            if (data.key === 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
                && data.channel === 'private'
                && data.data.chatapp_data.reciever == userId) {
                showNotificationToast(4, data.data.chatapp_data.user + ': ' + data.data.chatapp_data.message, 3000);
                fetchUnreadCount();
                messageSound.play().catch(e => console.warn("Sound play failed:", e));
            }
        });

        socket.on('json_data', (data) => {
            if (data.key === 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
                && data.channel === 'group'
                && data.data.chatapp_data.receivers.includes(userId)) {
                showNotificationToast(5, data.data.chatapp_data.user + ': ' + data.data.chatapp_data.message, 3000);
                fetchUnreadCount();
                messageSound.play().catch(e => console.warn("Sound play failed:", e));
            }
        });
        socket.on('json_data', (data) => {
            if (data.key === 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0='
                && data.channel === 'admin_notification'
                && data.data.chatapp_data.admins.includes(userId)) {
                showNotificationToast(5, data.data.chatapp_data.message, 3000);
                messageSound.play().catch(e => console.warn("Sound play failed:", e));
                if (window.location.pathname === '/admin') {
                    fetchUsers();
                }
            }
        });

        function fetchUnreadCount() {
            const chatbar = document.getElementById('chatbar');
            fetch('/unreadcount')
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch');
                    return response.json();
                })
                .then(data => {
                    if (data.total_unread > 0) {
                        chatbar.innerHTML = data.total_unread;
                        chatbar.style.display = 'flex';
                    } else {
                        chatbar.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching unread messages:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', fetchUnreadCount());
    </script>
    @yield('js')
</body>

</html>