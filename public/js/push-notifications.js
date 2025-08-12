function b64ToU8(b64){
  const p='='.repeat((4-b64.length%4)%4);
  const s=(b64+p).replace(/-/g,'+').replace(/_/g,'/');
  const r=atob(s);
  return Uint8Array.from([...r].map(c=>c.charCodeAt(0)));
}

async function sha256(str){
  const buf = new TextEncoder().encode(str);
  const hash = await crypto.subtle.digest('SHA-256', buf);
  return Array.from(new Uint8Array(hash)).map(b=>b.toString(16).padStart(2,'0')).join('');
}

async function ensurePushSubscription() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

  const reg = await navigator.serviceWorker.ready;

  // Reuse existing subscription if available
  let sub = await reg.pushManager.getSubscription();

  // Create only if missing and permission granted
  if (!sub) {
    if (Notification.permission !== 'granted') {
      const perm = await Notification.requestPermission();
      if (perm !== 'granted') return;
    }
    // Get your VAPID pub key from a meta tag or server-rendered var
    const vapid = document.querySelector('meta[name="vapid-public-key"]').content;
    sub = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: b64ToU8(vapid)
    });
  }

  // Post only if server doesn’t have this exact sub yet
  const vapid = document.querySelector('meta[name="vapid-public-key"]').content;
  const currentHash = await sha256(sub.endpoint + '|' + vapid);
  const savedHash   = localStorage.getItem('push:endpointHash');

  if (savedHash === currentHash) {
    // Already saved on server; nothing to do
    return;
  }

  const res = await fetch('/save-subscription', {  // or '/push/subscribe'
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    credentials: 'same-origin',
    body: JSON.stringify(sub.toJSON())
  });

  if (res.ok) {
    localStorage.setItem('push:endpointHash', currentHash);
    console.log('Subscription stored/updated');
  } else {
    console.error('Failed to store subscription', res.status, await res.text().catch(()=> ''));
  }
}

// Register SW once; call ensurePushSubscription on a user gesture or after login
if ('serviceWorker' in navigator && 'PushManager' in window) {
  navigator.serviceWorker.register('/sw.js?v=1').then(() => {
    //
  });
}

// Example triggers:
// 1) On first click or key press (no extra button needed)
window.addEventListener('click',  () => ensurePushSubscription(), { once: true });
window.addEventListener('keydown',() => ensurePushSubscription(), { once: true });

// 2) Or explicitly after a successful login:
window.addEventListener('user:logged-in', () => ensurePushSubscription());