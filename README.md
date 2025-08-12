# ASKSEO Chat App

A Laravel chat application with real-time sockets, installable PWA, offline fallback, and Web Push notifications.

## Features

- Realtime private and group messaging over Socket.IO  
- Installable PWA for desktop and mobile  
- Offline fallback page  
- Web Push notifications with VAPID  
- File sharing in chat  
- Authenticated user accounts

## Requirements

- PHP 8.2+ with required Laravel extensions  
- Composer  
- Node.js 18+ and npm  
- A supported database (MySQL, Postgres, etc.)  
- HTTPS for service workers and push (use ngrok in dev if needed)
- Socket server URL if you are using an external Socket.IO server

## Quick start

### 1) Clone and environment

```bash
git clone https://github.com/MalickMati/askseo_chat_websocket.git
cd askseo_chat_websocket
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at minimum:

```
APP_NAME="ASKSEO Chat"
APP_URL=https://your-domain-or-ngrok    # change this
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=askseo
DB_USERNAME=root
DB_PASSWORD=

# Web Push (VAPID)
VAPID_PUBLIC_KEY=BE...
VAPID_PRIVATE_KEY=...
VAPID_SUBJECT=https://your-domain
```

### 2) Install dependencies

```bash
composer update
npm ci || npm install
npm run build
```

> Tip: in CI or when you want deterministic installs, run `composer install` instead of `composer update`.

### 3) Database

```bash
php artisan migrate --seed
```

Optional if you use database queues or broadcasting tables:

```bash
php artisan queue:table
php artisan migrate
```

### 4) Storage symlink (if you serve uploaded files)

```bash
php artisan storage:link
```

### 5) Serve the app

Use your local web server or:

```bash
php artisan serve
```

If you test through ngrok, start a secure tunnel so service workers and push work:

```bash
ngrok http https://localhost:8000
```

Update `APP_URL` to the ngrok HTTPS URL while testing.

## PWA checklist

- `public/manifest.webmanifest` exists and returns `application/manifest+json`
- `public/sw.js` is registered in your layout
- Manifest includes 192 and 512 icons at the declared paths
- In Chrome DevTools, Application → Manifest shows parsed values
- iOS install uses Safari → Share → Add to Home Screen

## Web Push setup

1. Generate VAPID keys once:

   ```bash
   php artisan tinker
   >>> Minishlink\WebPush\VAPID::createVapidKeys();
   ```

   Put the keys in `.env` as shown above.

2. Expose the public key in your layout:

   ```html
   <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">
   ```

3. Subscribe users from the browser after a click and POST the subscription to your `/push/subscribe` (or `/save-subscription`) endpoint.

4. Send pushes from your chat flow or a test controller.

## Sockets

- Front end connects to your socket server, for example `https://socket.askseo.me`
- Ensure your reverse proxy forwards WebSocket upgrades (Upgrade and Connection headers)
- If polling works but WebSocket upgrade fails, check proxy and CORS

## Common commands

```bash
composer update
npm run build
php artisan migrate --seed
php artisan optimize:clear
php artisan queue:work     # if using queues
```

## Troubleshooting

- **Manifest parse error at line 1**  
  The URL is serving HTML instead of JSON. Serve with `application/manifest+json` and verify the path.

- **Install prompt never shows**  
  Ensure a service worker controls the page and the manifest is valid. On iOS there is no auto prompt, use Add to Home Screen.

- **Mixed content**  
  Use relative URLs (e.g., `/push/subscribe`). Do not hardcode `http://` on an HTTPS page.

- **Push saved on every load**  
  Call `getSubscription()` first and only POST to the server when the endpoint or VAPID key changes.

- **Duplicate notifications**  
  De-duplicate subscriptions per user or enforce a unique index.

## License

MIT. See `LICENSE` if present.