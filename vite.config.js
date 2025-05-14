// for production environment

// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/sass/app.scss', 'resources/js/app.js'],
//             refresh: false, // No need for HMR in production
//         }),
//     ],
//     build: {
//         manifest: true,
//         outDir: 'public/build',
//         assetsDir: 'assets', // stays default
//         emptyOutDir: true,
//         sourcemap: false,
//         minify: 'terser',
//         rollupOptions: {
//             input: {
//                 app: 'resources/js/app.js',
//                 style: 'resources/sass/app.scss',
//             },
//             output: {
//                 entryFileNames: 'assets/[name].[hash].js',
//                 chunkFileNames: 'assets/[name].[hash].js',
//                 assetFileNames: 'assets/[name].[hash][extname]',
//                 // Remove this if you're not using code splitting
//             },
//         },
//     },
// });



import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true, // Enables HMR for Livewire and Blade
        }),
    ],
    server: {
        host: process.env.VITE_HMR_HOST || 'localhost',
        port: process.env.VITE_HMR_PORT || 5173,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            port: process.env.VITE_HMR_PORT || 5173,
        },
    },
});
