import { execFile } from 'node:child_process';
import { setTimeout as delay } from 'node:timers/promises';
import { promisify } from 'node:util';
import { localLaravelPort, localVitePort, projectRoot } from './local-runtime.mjs';

const execFileAsync = promisify(execFile);
const localPorts = [...new Set([localLaravelPort, localVitePort])];

export function parseProcessInfo(output) {
    const match = output.trim().match(/^(\d+)\s+(\d+)\s+(.+)$/u);

    if (!match) {
        return undefined;
    }

    return {
        pid: Number(match[1]),
        parentPid: Number(match[2]),
        command: match[3],
    };
}

export function isProjectDevLocalProcess(processInfo, workingDirectory) {
    return (
        processInfo.command.includes('scripts/dev-local.mjs') && workingDirectory === projectRoot
    );
}

async function runInspectionCommand(command, args) {
    try {
        return await execFileAsync(command, args, { encoding: 'utf8' });
    } catch (error) {
        if (error.code === 1) {
            return { stdout: '', stderr: '' };
        }

        throw error;
    }
}

async function findListeningProcessIds(port) {
    const { stdout } = await runInspectionCommand('lsof', [
        '-nP',
        `-tiTCP:${port}`,
        '-sTCP:LISTEN',
    ]);

    return stdout.split(/\s+/u).filter(Boolean).map(Number).filter(Number.isInteger);
}

async function readProcessInfo(pid) {
    const { stdout } = await runInspectionCommand('ps', [
        '-p',
        String(pid),
        '-o',
        'pid=,ppid=,command=',
    ]);

    return parseProcessInfo(stdout);
}

async function readWorkingDirectory(pid) {
    const { stdout } = await runInspectionCommand('lsof', [
        '-a',
        '-p',
        String(pid),
        '-d',
        'cwd',
        '-Fn',
    ]);

    return stdout
        .split('\n')
        .find((line) => line.startsWith('n'))
        ?.slice(1);
}

async function findDevLocalCoordinator(startPid) {
    const visited = new Set();
    let pid = startPid;

    while (pid > 1 && !visited.has(pid)) {
        visited.add(pid);

        const processInfo = await readProcessInfo(pid);

        if (!processInfo) {
            return undefined;
        }

        if (isProjectDevLocalProcess(processInfo, await readWorkingDirectory(processInfo.pid))) {
            return processInfo.pid;
        }

        pid = processInfo.parentPid;
    }

    return undefined;
}

async function waitForPortsToClose(ports, timeoutMilliseconds = 5000) {
    const deadline = Date.now() + timeoutMilliseconds;

    while (Date.now() < deadline) {
        const listeners = await Promise.all(ports.map(findListeningProcessIds));

        if (listeners.every((processIds) => processIds.length === 0)) {
            return;
        }

        await delay(100);
    }

    throw new Error(`Timed out waiting for local ports ${ports.join(', ')} to close.`);
}

export async function stopLocalApplication() {
    const listenersByPort = new Map();

    for (const port of localPorts) {
        listenersByPort.set(port, await findListeningProcessIds(port));
    }

    const coordinators = new Set();
    const unmanagedListeners = [];

    for (const [port, processIds] of listenersByPort) {
        for (const processId of processIds) {
            const coordinator = await findDevLocalCoordinator(processId);

            if (coordinator) {
                coordinators.add(coordinator);
            } else {
                unmanagedListeners.push({ port, processId });
            }
        }
    }

    if (unmanagedListeners.length > 0) {
        const details = unmanagedListeners
            .map(({ port, processId }) => `${port} (PID ${processId})`)
            .join(', ');

        throw new Error(
            `Refusing to stop unmanaged processes on local application ports: ${details}.`,
        );
    }

    if (coordinators.size === 0) {
        return { stoppedProcessIds: [], ports: localPorts };
    }

    for (const processId of coordinators) {
        process.kill(processId, 'SIGTERM');
    }

    await waitForPortsToClose(localPorts);

    return { stoppedProcessIds: [...coordinators], ports: localPorts };
}
