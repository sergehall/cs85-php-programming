import { createServer } from 'node:net';
import { copyFile, readFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

export const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');

const mysqlHostPort = process.env.CS85_MYSQL_HOST_PORT || '3307';
const mysqlDatabase = process.env.CS85_MYSQL_DATABASE || 'cs85_php_programming';
const mysqlUser = process.env.CS85_MYSQL_USER || 'cs85';
const mysqlPassword = process.env.CS85_MYSQL_PASSWORD || 'cs85_password';
const mailpitSmtpPort = process.env.CS85_MAILPIT_SMTP_PORT || '1025';

export const localAppUrl = process.env.APP_URL || 'http://127.0.0.1:8000';
export const localVitePort = Number(process.env.VITE_PORT || 5173);
export const localLaravelPort = Number(new URL(localAppUrl).port || 8000);

export function localRuntimeEnv() {
    return {
        ...process.env,
        APP_URL: localAppUrl,
        DB_CONNECTION: 'mysql',
        DB_HOST: '127.0.0.1',
        DB_PORT: mysqlHostPort,
        DB_DATABASE: mysqlDatabase,
        DB_USERNAME: mysqlUser,
        DB_PASSWORD: mysqlPassword,
        QUEUE_CONNECTION: 'database',
        MAIL_MAILER: 'smtp',
        MAIL_HOST: '127.0.0.1',
        MAIL_PORT: mailpitSmtpPort,
    };
}

export function projectPath(...segments) {
    return join(projectRoot, ...segments);
}

export async function ensureEnvFile() {
    const envPath = projectPath('.env');

    if (existsSync(envPath)) {
        return false;
    }

    const examplePath = projectPath('.env.example');

    if (!existsSync(examplePath)) {
        throw new Error(
            'Missing .env and .env.example. Restore .env.example before starting locally.',
        );
    }

    await copyFile(examplePath, envPath);

    return true;
}

export async function readEnvValue(name) {
    const envPath = projectPath('.env');

    if (!existsSync(envPath)) {
        return undefined;
    }

    const content = await readFile(envPath, 'utf8');
    const match = content.match(new RegExp(`^${name}=([^\\n\\r]*)`, 'm'));

    if (!match) {
        return undefined;
    }

    return match[1].trim().replace(/^["']|["']$/g, '');
}

export async function assertPortAvailable(host, port, label) {
    await new Promise((resolvePromise, rejectPromise) => {
        const server = createServer();

        server.once('error', (error) => {
            if (error.code !== 'EADDRINUSE') {
                rejectPromise(
                    new Error(
                        `${label} port ${host}:${port} is not available (${error.code || error.message}).`,
                    ),
                );

                return;
            }

            rejectPromise(
                new Error(
                    `${label} port ${host}:${port} is already in use. Stop that process and retry.`,
                ),
            );
        });

        server.once('listening', () => {
            server.close(resolvePromise);
        });

        server.listen(port, host);
    });
}
