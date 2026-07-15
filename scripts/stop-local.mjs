import { stopLocalApplication } from './local-processes.mjs';

try {
    const { stoppedProcessIds, ports } = await stopLocalApplication();

    if (stoppedProcessIds.length === 0) {
        console.log(`Local application servers are already stopped. Ports: ${ports.join(', ')}.`);
    } else {
        console.log(
            `Stopped the local application coordinator (PID ${stoppedProcessIds.join(', ')}).`,
        );
        console.log(`Ports ${ports.join(', ')} are now available.`);
    }
} catch (error) {
    console.error(error.message);
    process.exitCode = 1;
}
