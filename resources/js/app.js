// resources/js/app.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
// resources/js/app.js
// resources/js/app.js
console.log('Echo initialized with:', {
    key: import.meta.env.VITE_REVERB_APP_KEY,
    host: import.meta.env.VITE_REVERB_HOST,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
});

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws'],
    disableStats: true,
});

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Connected to Reverb WebSocket at ws://127.0.0.1:8080');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Reverb connection error:', error);
});

window.Echo.channel('locations').subscribe(() => {
    console.log('Subscribed to locations channel');
});

window.Echo.channel('locations').listenToAll((eventName, data) => {
    console.log('Received event:', eventName, JSON.stringify(data));
});

// Maintain state for all devices
let currentLocations = {
    device1: { latitude: 37.7749, longitude: -122.4194, timestamp: '2025-04-13T12:00:00Z' },
    device2: { latitude: 34.0522, longitude: -118.2437, timestamp: '2025-04-13T12:01:00Z' },
    device3: { latitude: 40.7128, longitude: -74.006, timestamp: '2025-04-13T12:02:00Z' },
};

window.Echo.channel('locations').listen('.LocationUpdated', (event) => {
    console.log('Received LocationUpdated event:', JSON.stringify(event));
    currentLocations[event.deviceId] = {
        latitude: event.latitude,
        longitude: event.longitude,
        timestamp: event.timestamp,
    };
    window.Livewire.dispatch('locationsUpdated', { locations: currentLocations });
    console.log('Dispatched to Livewire:', JSON.stringify(currentLocations));
});

window.Echo.join('locations').here(() => {
    console.log('Joined locations channel');
});