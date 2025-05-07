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
        emptyOutDir: true, // Clean the output directory before building
        sourcemap: false, // Disable source maps for production
        minify: 'terser', // Use Terser for minification
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                style: 'resources/sass/app.scss',
            },
        },
    },
});
