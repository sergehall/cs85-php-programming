import { defineConfig } from 'vite';
import { createHash } from 'node:crypto';
import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

function manifestIntegrity() {
    return {
        name: 'manifest-integrity',
        apply: 'build',
        closeBundle() {
            const manifestPath = join(process.cwd(), 'public/build/manifest.json');

            if (!existsSync(manifestPath)) {
                return;
            }

            const manifest = JSON.parse(readFileSync(manifestPath, 'utf8'));
            const buildDirectory = dirname(manifestPath);

            for (const entry of Object.values(manifest)) {
                if (!entry.file) {
                    continue;
                }

                const assetPath = join(buildDirectory, entry.file);

                if (!existsSync(assetPath)) {
                    continue;
                }

                const hash = createHash('sha384').update(readFileSync(assetPath)).digest('base64');
                entry.integrity = `sha384-${hash}`;
            }

            writeFileSync(manifestPath, `${JSON.stringify(manifest, null, 2)}\n`);
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
        manifestIntegrity(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
