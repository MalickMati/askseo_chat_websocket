// JavaScript variable to track the user's initial status and current status
let userStatus = navigator.onLine ? 'online' : 'offline'; // Default status on page load

// Function to get CSRF token from meta tag
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

document.addEventListener('DOMContentLoaded', function(){
    updateUserStatus('online');
    notifyBackendOnline(); 
});

// Function to dynamically update the user status on the page (both avatar and status text)
function updateUserStatus(status) {
    const statusTextElement = document.getElementById('status-text');
    const userAvatarElement = document.getElementById('user-avatar');
    
    // Remove previous status classes (like 'online' or 'offline')
    statusTextElement.classList.remove('online', 'offline');
    userAvatarElement.classList.remove('online', 'offline');
    
    // Add the new status classes based on the passed status
    statusTextElement.classList.add(status);
    userAvatarElement.classList.add(status);
    
    // Update the status text content
    statusTextElement.textContent = status.charAt(0).toUpperCase() + status.slice(1); // Capitalize the first letter
}

// Function to send an offline status to the backend
function notifyBackendOffline() {
    const csrfToken = getCsrfToken();
    
    fetch('/update-status', { 
        method: 'POST', 
        body: JSON.stringify({ status: 'offline' }), 
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Add CSRF token here
        }
    })
    .then(response => response.json())
    .then(data => {
        //
    })
    .catch(error => {
        console.error('Error notifying backend:', error);
    });
}

// Function to send an online status to the backend
function notifyBackendOnline() {
    const csrfToken = getCsrfToken();
    
    fetch('/update-status', { 
        method: 'POST', 
        body: JSON.stringify({ status: 'online' }), 
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Add CSRF token here
        }
    })
    .then(response => response.json())
    .then(data => {
        //
    })
    .catch(error => {
        console.error('Error notifying backend:', error);
    });
}

// Handle page load: Check if the user is online or offline and update status accordingly
window.addEventListener('load', function () {
    // If the user is offline on page load, we mark them as online and notify the backend
    if (userStatus === 'offline') {
        console.log('User is offline on page load, marking as online...');
        updateUserStatus('online');
        notifyBackendOnline(); // Notify backend to mark the user as online
    } else {
        // If the user is already online, we just mark them as online
        updateUserStatus('online');
    }
});

// Monitor network changes (when user loses or regains internet connection)
window.addEventListener('offline', function () {
    userStatus = 'offline';
    updateUserStatus('offline');
    notifyBackendOffline(); // Mark as offline when network goes down
});

window.addEventListener('online', function () {
    userStatus = 'online';
    updateUserStatus('online');
    notifyBackendOnline(); // Mark as online when network is restored
});

// Detect when the tab is hidden (switched to another tab)
document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        // When the tab is inactive, mark the user as offline
        userStatus = 'offline';
        updateUserStatus('offline');
        notifyBackendOffline(); // Mark as offline when tab is hidden
    } else {
        // When the tab becomes active, check user’s status and mark as online if needed
        if (userStatus === 'offline') {
            updateUserStatus('online');
            notifyBackendOnline(); // Notify backend to mark the user as online
        }
    }
});

// Handle tab close or page unload
window.addEventListener("beforeunload", function (event) {
    notifyBackendOffline(); // Mark user offline when they close the tab or navigate away
});
