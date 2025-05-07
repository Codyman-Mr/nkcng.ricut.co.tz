
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

let reverbHost = '16.170.236.18';


window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    host: reverbHost,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
    wsHost: reverbHost,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    wsPath: '', // optional
    enabledTransports: ['ws', 'wss']
});

console.log('Echo initialized, connecting to Reverb...');

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Connected to Reverb WebSocket');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Reverb connection error:', error);
});

window.Echo.channel('locations').listen('LocationUpdated', (event) => {
    console.log('Received LocationUpdated event:', event);
});
