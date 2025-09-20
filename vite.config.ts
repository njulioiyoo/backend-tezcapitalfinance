import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5175,
        hmr: {
            host: 'cms.tez-capital.web.local',
            port: 5175,
        },
        watch: {
            usePolling: true,
            interval: 1000,
        },
        cors: {
            origin: ['http://cms.tez-capital.web.local', 'http://tez-capital.web.local', 'http://localhost:8983'],
            credentials: true
        }
    },
});
