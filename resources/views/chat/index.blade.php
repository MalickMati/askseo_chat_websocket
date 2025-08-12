@extends('layouts.app')

@section('content')
    <x-chat_sidebar :currentUser=$currentUser :users=$allusers :groups=$allgroups></x-chat_sidebar>
    <x-chat_area></x-chat_area>
    <x-chat_sidebar_menu :tasks="$tasks"></x-chat_sidebar_menu>
    <x-chat_menu></x-chat_menu>
    <x-modals.add-user-in-group-modal />
    <x-modals.group-members-modal />
    <x-modals.removemember />

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
    <!-- Media Modal -->
    <div id="mediaModal" class="media-modal">
        <div class="media-modal-backdrop"></div>
        <div class="media-modal-content">
            <span class="media-modal-close">&times;</span>
            <div id="mediaContainer"></div>
        </div>
    </div>
@endsection

@if (session()->has('user_type') && (session('user_type') === 'admin' || session('user_type') === 'moderator') || session('user_type') === 'super_admin')
    <script>
        const isAdmin = true;
    </script>

@else
    <script>
        const isAdmin = false;
    </script>
@endif

@section('js')
    <script>
        // Global variables
        let currentUserId = {{ $currentUser['id'] }};
        let usersMap = {
            @foreach ($allusers as $user)
                {{ $user['id'] }}: "{{ addslashes($user['username']) }}",
            @endforeach
                        {{ $currentUser['id'] }}: "{{ addslashes($currentUser['username']) }}"
                    };
        const icon = "{{ secure_asset('favicon.ico') }}";

    </script>

    <script src="{{ secure_asset('js/script.js') }}"></script>
    <script src="{{ secure_asset('js/user-status-change.js') }}"></script>
    <script src="{{ secure_asset('js/push-notifications.js') }}"></script>

    <script>
        socket.on('json_data', (data) => {
            if (
                data.key === 'rmNujkRchUmUD89lcDaxakq6yl1grPM/eumvQ1t8Sz0=' &&
                data.channel === 'private' &&
                data.data.chatapp_data.reciever == currentUserId
            ) {
                if (document.hidden) {
                    navigator.serviceWorker.ready.then(registration => {
                        registration.showNotification(
                            "New message from " + data.data.chatapp_data.sender_name,
                            {
                                body: data.data.chatapp_data.message || "📩 New attachment",
                                icon: "/assets/images/icon-192.png",
                                badge: "/assets/images/icon-192.png",
                                data: { url: "/chat"}
                            }
                        );
                    });
                } else {
                    console.log("Message received in active tab — no push");
                }
            }
        });
    </script>

    @if(session('error'))
        <script>
            showNotificationToast(2, "{{ session('error') }}", 10000);
        </script>
    @endif

@endsection