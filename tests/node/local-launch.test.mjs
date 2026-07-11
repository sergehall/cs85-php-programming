import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { describe, it } from 'node:test';

describe('local launch scripts', () => {
    it('keeps local startup orchestration in Node instead of inline shell env', async () => {
        const packageJson = JSON.parse(await readFile('package.json', 'utf8'));

        assert.equal(packageJson.scripts['dev-local'], 'node scripts/dev-local.mjs');
        assert.equal(
            packageJson.scripts['dev-local:no-open'],
            'node scripts/dev-local.mjs --no-open',
        );
        assert.equal(
            packageJson.scripts['db:migrate:local'],
            'node scripts/dev-local.mjs --migrate-only',
        );
        assert.doesNotMatch(
            packageJson.scripts['dev-local'],
            /DB_PASSWORD|DB_USERNAME|DB_DATABASE/u,
        );
        assert.doesNotMatch(
            packageJson.scripts['db:migrate:local'],
            /DB_PASSWORD|DB_USERNAME|DB_DATABASE/u,
        );
    });

    it('uses the Laravel health route before opening the browser', async () => {
        const openScript = await readFile('scripts/open-local-app.mjs', 'utf8');

        assert.match(openScript, /APP_OPEN_HEALTH_URL/u);
        assert.match(openScript, /new URL\('\/up', appUrl\)/u);
        assert.match(openScript, /response\.status >= 200 && response\.status < 500/u);
    });

    it('centralizes local runtime defaults for Laravel and infrastructure', async () => {
        const runtimeScript = await readFile('scripts/local-runtime.mjs', 'utf8');

        assert.match(runtimeScript, /DB_CONNECTION: 'mysql'/u);
        assert.match(runtimeScript, /DB_HOST: '127\.0\.0\.1'/u);
        assert.match(runtimeScript, /QUEUE_CONNECTION: 'database'/u);
        assert.match(runtimeScript, /MAIL_MAILER: 'smtp'/u);
        assert.match(runtimeScript, /CS85_MYSQL_HOST_PORT/u);
        assert.match(runtimeScript, /EADDRINUSE/u);
    });

    it('can skip browser opening for headless launch checks', async () => {
        const devScript = await readFile('scripts/dev-local.mjs', 'utf8');

        assert.match(devScript, /process\.argv\.includes\('--no-open'\)/u);
        assert.match(devScript, /APP_OPEN_BROWSER !== 'false'/u);
        assert.match(devScript, /scripts\/open-local-app\.mjs/u);
    });
});
