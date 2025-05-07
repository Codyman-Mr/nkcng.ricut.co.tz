// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';

// window.Pusher = Pusher;



// console.log('VITE_REVERB_HOST:', import.meta.env.VITE_REVERB_HOST)

// console.log('Echo initialized with:', {
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     host: import.meta.env.VITE_REVERB_HOST,
//     port: import.meta.env.VITE_REVERB_PORT,
//     scheme: import.meta.env.VITE_REVERB_SCHEME,
// });

// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
//     wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
//     wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
//     forceTLS: false,
//     enabledTransports: ['ws'],
//     disableStats: true,
// });

// window.Echo.connector.pusher.connection.bind('connected', () => {
//     console.log('Connected to Reverb WebSocket at ws://127.0.0.1:8080');
// });

// window.Echo.connector.pusher.connection.bind('error', (error) => {
//     console.error('Reverb connection error:', error);
// });

// window.Echo.channel('locations').subscribe(() => {
//     console.log('Subscribed to locations channel');
// });

// window.Echo.channel('locations').listenToAll((eventName, data) => {
//     console.log('Received event:', eventName, JSON.stringify(data));
// });

// let currentLocations = {

// };

// window.Echo.channel('locations').listen('.LocationUpdated', (event) => {
//     console.log('Received LocationUpdated event:', JSON.stringify(event));
//     currentLocations[event.deviceId] = {
//         latitude: event.latitude,
//         longitude: event.longitude,
//         timestamp: event.timestamp,
//     };
//     window.Livewire.dispatch('locationsUpdated', { locations: currentLocations });
//     console.log('Dispatched to Livewire:', JSON.stringify(currentLocations));
// });

// window.Echo.join('locations').here(() => {
//     console.log('Joined locations channel');
// });

//nothing to do here



import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

console.log('Environment variables:', {
    key: import.meta.env.VITE_REVERB_APP_KEY,
    host: import.meta.env.VITE_REVERB_HOST,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
});

if (!import.meta.env.VITE_REVERB_HOST) {
    console.error('VITE_REVERB_HOST is not defined in .env');
}

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'wss',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log(`Connected to Reverb WebSocket at ${import.meta.env.VITE_REVERB_SCHEME}://${import.meta.env.VITE_REVERB_HOST}:${import.meta.env.VITE_REVERB_PORT}`);
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

let currentLocations = {};

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
