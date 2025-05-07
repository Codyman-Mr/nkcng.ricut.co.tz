import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import dotenv from 'dotenv';

// const env = dotenv.parse(fs.readFileSync('/home/ec2-user/nkcng-2/.env'));

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // define: {
    //     'import.meta.env.VITE_REVERB_APP_KEY': JSON.stringify(env.VITE_REVERB_APP_KEY),
    //     'import.meta.env.VITE_REVERB_HOST': JSON.stringify(env.VITE_REVERB_HOST),
    //     'import.meta.env.VITE_REVERB_PORT': JSON.stringify(env.VITE_REVERB_PORT),
    //     'import.meta.env.VITE_REVERB_SCHEME': JSON.stringify(env.VITE_REVERB_SCHEME),
    // },
});


// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import fs from 'fs';
// import path from 'path';

// // Use relative path for .env
// const envPath = path.resolve(process.cwd(), '.env');

// // Debug: Check if .env exists
// if (!fs.existsSync(envPath)) {
//     console.error(`.env file not found at ${envPath}`);
// } else {
//     console.log(`.env file found at ${envPath}`);
// }

// // Load .env manually
// const env = {};
// fs.readFileSync(envPath, 'utf-8')
//     .split('\n')
//     .forEach(line => {
//         const [key, value] = line.split('=');
//         if (key && key.startsWith('VITE_')) {
//             env[key] = value;
//         }
//     });

// console.log('Loaded environment variables:', env);

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     define: {
//         'import.meta.env.VITE_REVERB_APP_KEY': JSON.stringify(env.VITE_REVERB_APP_KEY),
//         'import.meta.env.VITE_REVERB_HOST': JSON.stringify(env.VITE_REVERB_HOST),
//         'import.meta.env.VITE_REVERB_PORT': JSON.stringify(env.VITE_REVERB_PORT),
//         'import.meta.env.VITE_REVERB_SCHEME': JSON.stringify(env.VITE_REVERB_SCHEME),
//     },
// });
