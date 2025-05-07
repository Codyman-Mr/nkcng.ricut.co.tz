import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: false, // No need for HMR in production
        }),
    ],
    build: {
        manifest: true, // Required for Laravel to find assets in production
        outDir: 'public/build', // Default; just to be explicit
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                style: 'resources/sass/app.scss',
            },
        },
    },
});
