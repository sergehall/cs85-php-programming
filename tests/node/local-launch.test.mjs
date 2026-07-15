import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { describe, it } from 'node:test';
import { isProjectDevLocalProcess, parseProcessInfo } from '../../scripts/local-processes.mjs';
import { isEnvFlagEnabled, localRuntimeEnv } from '../../scripts/local-runtime.mjs';

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
        assert.equal(packageJson.scripts['stop:app'], 'node scripts/stop-local.mjs');
        assert.equal(packageJson.scripts['restart:app'], 'node scripts/restart-local.mjs');
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

    it('keeps Mailpit safe by default and preserves external SMTP when opted out', () => {
        const externalSmtp = {
            MAIL_MAILER: 'smtp',
            MAIL_HOST: 'smtp.example.com',
            MAIL_PORT: '587',
        };

        const mailpitEnv = localRuntimeEnv({ baseEnv: externalSmtp });
        const externalEnv = localRuntimeEnv({ baseEnv: externalSmtp, useMailpit: false });

        assert.equal(mailpitEnv.MAIL_HOST, '127.0.0.1');
        assert.equal(mailpitEnv.MAIL_PORT, '1025');
        assert.equal(externalEnv.MAIL_HOST, 'smtp.example.com');
        assert.equal(externalEnv.MAIL_PORT, '587');
        assert.equal(isEnvFlagEnabled(undefined), true);
        assert.equal(isEnvFlagEnabled('false'), false);
        assert.equal(isEnvFlagEnabled('0'), false);
    });

    it('can skip browser opening for headless launch checks', async () => {
        const devScript = await readFile('scripts/dev-local.mjs', 'utf8');

        assert.match(devScript, /process\.argv\.includes\('--no-open'\)/u);
        assert.match(devScript, /APP_OPEN_BROWSER !== 'false'/u);
        assert.match(devScript, /scripts\/open-local-app\.mjs/u);
    });

    it('identifies only this project dev-local coordinator as managed', () => {
        const processInfo = parseProcessInfo('22388 21689 node scripts/dev-local.mjs --no-open');

        assert.deepEqual(processInfo, {
            pid: 22388,
            parentPid: 21689,
            command: 'node scripts/dev-local.mjs --no-open',
        });
        assert.equal(isProjectDevLocalProcess(processInfo, process.cwd()), true);
        assert.equal(isProjectDevLocalProcess(processInfo, '/tmp/another-project'), false);
        assert.equal(
            isProjectDevLocalProcess(
                { ...processInfo, command: 'php artisan serve --port=8000' },
                process.cwd(),
            ),
            false,
        );
    });
});
