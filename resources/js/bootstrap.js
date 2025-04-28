
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.VITE_REVERB_APP_KEY,
    wsHost: process.env.VITE_REVERB_HOST,
    wsPort: process.env.VITE_REVERB_PORT,
    wssPort: process.env.VITE_REVERB_PORT,
    scheme: process.env.VITE_REVERB_SCHEME,
    enabledTransports: ['ws', 'wss'],
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