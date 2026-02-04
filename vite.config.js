import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/landing/main.jsx'
            ],
            refresh: true,
        }),
        react({
            include: '**/*.{jsx,tsx}',
            jsxRuntime: 'classic',
        }),
    ],
    server: {
        strictPort: false,
        hmr: {
            overlay: true,
        },
    },
    optimizeDeps: {
        force: true,
    },
    cacheDir: 'node_modules/.vite',
});
