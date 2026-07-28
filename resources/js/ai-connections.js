const STATE_CLASSES = {
    connected: {
        container: ['text-emerald-800'],
        dot: ['bg-emerald-500'],
        label: 'Connected',
    },
    checking: {
        container: ['text-amber-800'],
        dot: ['bg-amber-500'],
        label: 'Checking connection…',
    },
    unavailable: {
        container: ['text-orange-800'],
        dot: ['bg-orange-500'],
        label: 'Unavailable',
    },
};

const ALL_CONTAINER_CLASSES = Object.values(STATE_CLASSES).flatMap((state) => state.container);
const ALL_DOT_CLASSES = Object.values(STATE_CLASSES).flatMap((state) => state.dot);

export const connectionSummary = (connected, total) =>
    connected === total
        ? `All ${total} models connected`
        : `${connected} of ${total} models connected`;

export const initAiConnections = (
    root = document.querySelector('[data-ai-connections]'),
    fetcher = globalThis.fetch,
) => {
    if (!root || typeof fetcher !== 'function') {
        return;
    }

    const endpoint = root.dataset.statusEndpoint;
    const refresh = root.querySelector('[data-ai-connections-refresh]');
    const summary = root.querySelector('[data-ai-connections-summary]');
    let activeController;

    const setModelState = (mode, tone, label, message) => {
        const card = root.querySelector(`[data-ai-connection-model="${mode}"]`);
        const state = card?.querySelector('[data-ai-connection-state]');
        const dot = card?.querySelector('[data-ai-connection-dot]');
        const stateLabel = card?.querySelector('[data-ai-connection-label]');
        const detail = card?.querySelector('[data-ai-connection-message]');
        const style = STATE_CLASSES[tone] ?? STATE_CLASSES.unavailable;

        state?.classList.remove(...ALL_CONTAINER_CLASSES);
        state?.classList.add(...style.container);
        dot?.classList.remove(...ALL_DOT_CLASSES);
        dot?.classList.add(...style.dot);

        if (stateLabel) {
            stateLabel.textContent = label || style.label;
        }

        if (detail) {
            detail.textContent = message;
        }
    };

    const setChecking = () => {
        root.querySelectorAll('[data-ai-connection-model]').forEach((card) => {
            setModelState(
                card.dataset.aiConnectionModel,
                'checking',
                STATE_CLASSES.checking.label,
                'Contacting the provider from Laravel…',
            );
        });

        if (summary) {
            summary.textContent = 'Checking 4 models…';
            summary.className =
                'rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-900';
        }
    };

    const check = async () => {
        if (!endpoint) {
            return;
        }

        activeController?.abort();
        activeController = new AbortController();
        refresh?.setAttribute('disabled', '');
        setChecking();

        try {
            const response = await fetcher(endpoint, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeController.signal,
            });

            if (!response.ok) {
                throw new Error(`Connection check failed with HTTP ${response.status}.`);
            }

            const result = await response.json();
            result.models.forEach((model) => {
                const latency = Number.isInteger(model.latency_ms)
                    ? ` Checked in ${model.latency_ms} ms.`
                    : '';
                setModelState(
                    model.mode,
                    model.connected ? 'connected' : 'unavailable',
                    model.connected ? 'Connected' : 'Unavailable',
                    `${model.message}${latency}`,
                );
            });

            if (summary) {
                const { connected, total } = result.summary;
                summary.textContent = connectionSummary(connected, total);
                summary.className =
                    connected === total
                        ? 'rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-900'
                        : 'rounded-full border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-900';
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            root.querySelectorAll('[data-ai-connection-model]').forEach((card) => {
                setModelState(
                    card.dataset.aiConnectionModel,
                    'unavailable',
                    'Check failed',
                    'Laravel could not complete the provider health check.',
                );
            });

            if (summary) {
                summary.textContent = 'Connection check unavailable';
                summary.className =
                    'rounded-full border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-900';
            }
        } finally {
            refresh?.removeAttribute('disabled');
        }
    };

    refresh?.addEventListener('click', check);
    check();
};
