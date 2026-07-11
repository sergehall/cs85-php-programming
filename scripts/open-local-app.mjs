import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);
const appUrl = process.env.APP_URL || 'http://127.0.0.1:8000';
const healthUrl = process.env.APP_OPEN_HEALTH_URL || new URL('/up', appUrl).toString();
const timeoutMs = Number(process.env.APP_OPEN_TIMEOUT_MS || 30000);
const pollEveryMs = 500;

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

async function waitForApp() {
    const startedAt = Date.now();

    while (Date.now() - startedAt < timeoutMs) {
        try {
            const response = await fetch(healthUrl, {
                method: 'GET',
                signal: AbortSignal.timeout(pollEveryMs),
            });

            if (response.status >= 200 && response.status < 500) {
                return;
            }
        } catch {
            await sleep(pollEveryMs);
        }
    }

    throw new Error(`Timed out waiting for ${healthUrl}`);
}

await waitForApp();
await execFileAsync('open', [appUrl]);
console.log(`Opened ${appUrl}`);
