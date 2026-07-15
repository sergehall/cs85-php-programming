import { spawn } from 'node:child_process';
import { existsSync } from 'node:fs';
import { createInterface } from 'node:readline';
import {
    assertPortAvailable,
    ensureEnvFile,
    isEnvFlagEnabled,
    localAppUrl,
    localLaravelPort,
    localRuntimeEnv,
    localVitePort,
    projectPath,
    projectRoot,
    readEnvValue,
} from './local-runtime.mjs';

let env = {};
let useMailpit = true;
const children = new Map();
let shuttingDown = false;
const migrateOnly = process.argv.includes('--migrate-only');
const shouldOpenBrowser =
    !process.argv.includes('--no-open') && process.env.APP_OPEN_BROWSER !== 'false';

function runStep(label, command, args, options = {}) {
    console.log(`\n==> ${label}`);

    return new Promise((resolvePromise, rejectPromise) => {
        const child = spawn(command, args, {
            cwd: projectRoot,
            env,
            stdio: 'inherit',
            ...options,
        });

        child.once('error', rejectPromise);
        child.once('exit', (code, signal) => {
            if (code === 0) {
                resolvePromise();

                return;
            }

            rejectPromise(new Error(`${label} failed with ${signal || `exit code ${code}`}.`));
        });
    });
}

function prefixStream(stream, name) {
    const lines = createInterface({ input: stream });

    lines.on('line', (line) => {
        console.log(`[${name}] ${line}`);
    });
}

function startProcess(name, command, args, { required = true } = {}) {
    const child = spawn(command, args, {
        cwd: projectRoot,
        env,
        stdio: ['ignore', 'pipe', 'pipe'],
    });

    children.set(name, { child, required });
    prefixStream(child.stdout, name);
    prefixStream(child.stderr, name);

    child.once('error', (error) => {
        console.error(`[${name}] ${error.message}`);
        stopAll(1);
    });

    child.once('exit', (code, signal) => {
        children.delete(name);

        if (shuttingDown || !required) {
            return;
        }

        console.error(`[${name}] stopped unexpectedly with ${signal || `exit code ${code}`}.`);
        stopAll(code || 1);
    });
}

function stopAll(exitCode = 0) {
    if (shuttingDown) {
        return;
    }

    shuttingDown = true;

    for (const { child } of children.values()) {
        if (!child.killed) {
            child.kill('SIGTERM');
        }
    }

    setTimeout(() => process.exit(exitCode), 250);
}

async function preflight() {
    await ensureEnvFile();

    useMailpit = isEnvFlagEnabled(await readEnvValue('CS85_USE_MAILPIT'), true);
    env = localRuntimeEnv({ useMailpit });

    if (!existsSync(projectPath('vendor/autoload.php'))) {
        throw new Error(
            'Missing vendor/autoload.php. Run composer install before starting locally.',
        );
    }

    const appKey = await readEnvValue('APP_KEY');

    if (!appKey) {
        await runStep('Generate Laravel application key', 'php', [
            'artisan',
            'key:generate',
            '--ansi',
        ]);
    }

    if (migrateOnly) {
        return;
    }

    if (!existsSync(projectPath('node_modules/.bin/vite'))) {
        throw new Error('Missing Vite binary. Run npm install before starting locally.');
    }

    await assertPortAvailable('127.0.0.1', localLaravelPort, 'Laravel');
    await assertPortAvailable('127.0.0.1', localVitePort, 'Vite');
}

process.once('SIGINT', () => stopAll(0));
process.once('SIGTERM', () => stopAll(0));

await preflight();

if (migrateOnly) {
    await runStep('Apply local database migrations', 'php', ['artisan', 'migrate', '--force']);
    process.exit(0);
}

await runStep('Start Docker infrastructure', 'npm', ['run', 'infra:up']);
await runStep('Apply local database migrations', 'php', ['artisan', 'migrate', '--force']);

console.log(`\n==> Start application servers`);
console.log(`Laravel: ${localAppUrl}`);
console.log(`Vite: http://127.0.0.1:${localVitePort}`);
console.log(`Mail: ${useMailpit ? 'Mailpit at http://127.0.0.1:8025' : 'external SMTP from .env'}`);

startProcess('laravel', 'php', [
    'artisan',
    'serve',
    '--host=127.0.0.1',
    `--port=${localLaravelPort}`,
]);
startProcess('vite', projectPath('node_modules/.bin/vite'), [
    '--host',
    '127.0.0.1',
    '--port',
    String(localVitePort),
    '--strictPort',
]);

if (shouldOpenBrowser) {
    startProcess('open', 'node', ['scripts/open-local-app.mjs'], { required: false });
}
