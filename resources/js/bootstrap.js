
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    host: import.meta.env.VITE_REVERB_HOST,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
    wsHost: import.meta.env.VITE_REVERB_HOST,
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
