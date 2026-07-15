import { spawn } from 'node:child_process';
import { projectPath, projectRoot } from './local-runtime.mjs';
import { stopLocalApplication } from './local-processes.mjs';

try {
    const { stoppedProcessIds } = await stopLocalApplication();

    if (stoppedProcessIds.length > 0) {
        console.log(`Stopped existing local application process: ${stoppedProcessIds.join(', ')}.`);
    }

    console.log('Starting a fresh local application stack.');

    const child = spawn(
        process.execPath,
        [projectPath('scripts/dev-local.mjs'), ...process.argv.slice(2)],
        {
            cwd: projectRoot,
            env: process.env,
            stdio: 'inherit',
        },
    );

    const forwardSignal = (signal) => {
        if (!child.killed) {
            child.kill(signal);
        }
    };

    process.once('SIGINT', () => forwardSignal('SIGINT'));
    process.once('SIGTERM', () => forwardSignal('SIGTERM'));

    child.once('error', (error) => {
        console.error(`Unable to restart the local application: ${error.message}`);
        process.exitCode = 1;
    });

    child.once('exit', (code, signal) => {
        if (signal) {
            process.kill(process.pid, signal);

            return;
        }

        process.exitCode = code ?? 1;
    });
} catch (error) {
    console.error(error.message);
    process.exitCode = 1;
}
