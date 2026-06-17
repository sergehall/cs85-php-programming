import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);
const databaseName = process.env.DB_DATABASE || 'cs85_php_programming';
const mysqlUser = process.env.DB_USERNAME || 'root';
const mysqlHost = process.env.DB_HOST || '127.0.0.1';
const mysqlPort = process.env.DB_PORT || '3306';

async function run(command, args) {
    const { stdout, stderr } = await execFileAsync(command, args);

    if (stdout.trim()) {
        console.log(stdout.trim());
    }

    if (stderr.trim()) {
        console.error(stderr.trim());
    }
}

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

async function waitForMysql() {
    const startedAt = Date.now();
    const timeoutMs = 20000;

    while (Date.now() - startedAt < timeoutMs) {
        try {
            await execFileAsync('mysqladmin', [
                '--protocol=tcp',
                '-h',
                mysqlHost,
                '-P',
                mysqlPort,
                '-u',
                mysqlUser,
                'ping',
            ]);

            return;
        } catch {
            await sleep(500);
        }
    }

    throw new Error(`Timed out waiting for MySQL at ${mysqlHost}:${mysqlPort}`);
}

await run('brew', ['services', 'start', 'mysql']);
await waitForMysql();
await run('mysql', [
    '--protocol=tcp',
    '-h',
    mysqlHost,
    '-P',
    mysqlPort,
    '-u',
    mysqlUser,
    '-e',
    `CREATE DATABASE IF NOT EXISTS \`${databaseName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`,
]);

console.log(`Infrastructure is ready. MySQL database: ${databaseName}`);
