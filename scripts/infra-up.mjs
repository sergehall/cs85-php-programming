import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);
const databaseName = process.env.CS85_MYSQL_DATABASE || 'cs85_php_programming';
const mysqlUser = process.env.CS85_MYSQL_USER || 'cs85';
const mysqlPassword = process.env.CS85_MYSQL_PASSWORD || 'cs85_password';
const mysqlHostPort = process.env.CS85_MYSQL_HOST_PORT || '3307';

async function run(command, args) {
    const { stdout, stderr } = await execFileAsync(command, args);

    if (stdout.trim()) {
        console.log(stdout.trim());
    }

    if (stderr.trim()) {
        console.error(stderr.trim());
    }
}

async function commandSucceeds(command, args) {
    try {
        await execFileAsync(command, args);

        return true;
    } catch {
        return false;
    }
}

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

async function ensureDockerDaemon() {
    if (await commandSucceeds('docker', ['info'])) {
        return;
    }

    console.log('Docker daemon is not ready. Opening Docker Desktop...');

    try {
        await execFileAsync('open', ['-a', 'Docker']);
    } catch {
        throw new Error(
            'Docker is not running. Start Docker Desktop and run npm run infra:up again.',
        );
    }

    const startedAt = Date.now();
    const timeoutMs = 120000;

    while (Date.now() - startedAt < timeoutMs) {
        if (await commandSucceeds('docker', ['info'])) {
            return;
        }

        await sleep(2000);
    }

    throw new Error('Timed out waiting for Docker Desktop to become ready.');
}

async function waitForMysql() {
    const startedAt = Date.now();
    const timeoutMs = 60000;

    while (Date.now() - startedAt < timeoutMs) {
        try {
            await execFileAsync('docker', [
                'compose',
                'exec',
                '-T',
                'mysql',
                'mysqladmin',
                'ping',
                '-h',
                '127.0.0.1',
                '-u',
                mysqlUser,
                `-p${mysqlPassword}`,
            ]);

            return;
        } catch {
            await sleep(500);
        }
    }

    throw new Error(`Timed out waiting for Docker MySQL on 127.0.0.1:${mysqlHostPort}`);
}

await ensureDockerDaemon();
await run('docker', ['compose', 'up', '-d', '--no-recreate']);
await waitForMysql();
await run('docker', ['compose', 'ps']);

console.log('Existing Docker containers are reused. Volumes are preserved.');
console.log(`Infrastructure is ready. MySQL database: ${databaseName}`);
console.log(`Laravel DB host: 127.0.0.1:${mysqlHostPort}`);
console.log('Adminer: http://127.0.0.1:8081');
console.log('Mailpit: http://127.0.0.1:8025');
