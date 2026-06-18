import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);

async function run(command, args) {
    const { stdout, stderr } = await execFileAsync(command, args);

    if (stdout.trim()) {
        console.log(stdout.trim());
    }

    if (stderr.trim()) {
        console.error(stderr.trim());
    }
}

await run('docker', ['compose', 'down']);
console.log('Infrastructure stopped. Docker services are down. Volumes are preserved.');
