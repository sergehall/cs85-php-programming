const STATUS_DOT_CLASSES = ['bg-emerald-500', 'bg-amber-500', 'bg-orange-500'];

export const parseSseEventBlock = (block) => {
    const data = block
        .split(/\r?\n/)
        .filter((line) => line.startsWith('data:'))
        .map((line) => line.slice(5).trimStart())
        .join('\n');

    if (!data) {
        return null;
    }

    const event = JSON.parse(data);

    if (!event || typeof event !== 'object' || typeof event.type !== 'string') {
        throw new TypeError('The AI stream returned an invalid event.');
    }

    return event;
};

export const characterCountLabel = (length, limit) =>
    `${length.toLocaleString('en-US')} / ${limit.toLocaleString('en-US')}`;

export const initAiChat = (root = document.querySelector('[data-ai-chat]')) => {
    if (!root) {
        return;
    }

    const form = root.querySelector('[data-ai-message-form]');
    const messages = root.querySelector('[data-ai-messages]');
    const textarea = root.querySelector('[data-ai-textarea]');
    const submit = root.querySelector('[data-ai-submit]');
    const submitLabel = root.querySelector('[data-ai-submit-label]');
    const stop = root.querySelector('[data-ai-stop]');
    const status = root.querySelector('[data-ai-status]');
    const statusDot = root.querySelector('[data-ai-status-dot]');
    const characterCount = root.querySelector('[data-ai-character-count]');
    const scrollLatest = root.querySelector('[data-ai-scroll-latest]');
    const messageCount = root.querySelector('[data-ai-message-count]');
    const promptLimit = Number(root.dataset.promptLimit || textarea?.maxLength || 0);
    let activeRequest;

    const setStatus = (message, tone = 'ready') => {
        if (status) {
            status.textContent = message;
        }

        if (statusDot) {
            statusDot.classList.remove(...STATUS_DOT_CLASSES);
            statusDot.classList.add(
                tone === 'busy'
                    ? 'bg-amber-500'
                    : tone === 'error'
                      ? 'bg-orange-500'
                      : 'bg-emerald-500',
            );
        }
    };

    const resizeTextarea = () => {
        if (!textarea) {
            return;
        }

        textarea.style.height = 'auto';
        textarea.style.height = `${Math.min(textarea.scrollHeight, 240)}px`;

        if (characterCount && promptLimit > 0) {
            characterCount.textContent = characterCountLabel(textarea.value.length, promptLimit);
            characterCount.classList.toggle(
                'text-orange-700',
                textarea.value.length >= promptLimit * 0.9,
            );
        }
    };

    const isNearLatestMessage = () =>
        !messages || messages.scrollHeight - messages.scrollTop - messages.clientHeight < 120;

    const scrollToLatestMessage = (behavior = 'smooth') => {
        messages?.scrollTo({ top: messages.scrollHeight, behavior });
    };

    const appendMessage = (templateSelector, content) => {
        const template = root.querySelector(templateSelector);

        if (!template || !messages) {
            return null;
        }

        messages.querySelector('[data-ai-empty-state]')?.remove();
        const fragment = template.content.cloneNode(true);
        const contentElement = fragment.querySelector('[data-ai-message-content]');

        if (contentElement) {
            contentElement.textContent = content;
        }

        messages.append(fragment);
        const article = messages.lastElementChild;
        scrollToLatestMessage();

        return {
            article,
            content: article?.querySelector('[data-ai-message-content]') ?? null,
        };
    };

    const finishAssistantMessage = (message, state) => {
        message.article?.removeAttribute('data-ai-streaming');
        const stateElement = message.article?.querySelector('[data-ai-message-state]');
        const copyButton = message.article?.querySelector('[data-ai-copy]');

        if (stateElement) {
            stateElement.textContent = state;
        }

        if (message.content?.textContent && copyButton) {
            copyButton.classList.remove('hidden');
        }
    };

    const updateMessageCount = () => {
        if (!messageCount || !messages) {
            return;
        }

        const count = messages.querySelectorAll('[data-ai-message-role]').length;
        messageCount.textContent = `${count.toLocaleString('en-US')} messages`;
    };

    const markAssistantError = (message, errorMessage) => {
        if (message.content) {
            message.content.textContent = errorMessage;
            message.content.classList.add('border-orange-300', 'bg-orange-50', 'text-orange-900');
        }

        finishAssistantMessage(message, 'Unavailable');
    };

    const setBusy = (busy) => {
        if (submit) {
            submit.disabled = busy;
            submit.setAttribute('aria-busy', String(busy));
        }

        if (submitLabel) {
            submitLabel.textContent = busy ? 'Thinking' : 'Send';
        }

        stop?.classList.toggle('hidden', !busy);
        textarea?.setAttribute('aria-busy', String(busy));
    };

    const updateConversationTitle = (conversationTitle) => {
        if (typeof conversationTitle !== 'string' || !conversationTitle.trim()) {
            return;
        }

        const title = root.querySelector('[data-ai-conversation-title]');
        const activeHistoryItem = root.querySelector(
            '[data-ai-conversation-item][aria-current="page"]',
        );
        const activeHistoryTitle = activeHistoryItem?.querySelector('span > span');

        if (title) {
            title.textContent = conversationTitle;
        }

        if (activeHistoryTitle) {
            activeHistoryTitle.textContent = conversationTitle;
            activeHistoryItem.dataset.conversationTitle = conversationTitle.toLowerCase();
        }
    };

    const handleStreamEvent = (streamEvent, assistantMessage) => {
        if (streamEvent.type === 'delta' && typeof streamEvent.content === 'string') {
            const shouldFollow = isNearLatestMessage();
            assistantMessage.content.textContent += streamEvent.content;
            const stateElement = assistantMessage.article?.querySelector('[data-ai-message-state]');

            if (stateElement) {
                stateElement.textContent = 'Writing';
            }

            setStatus('Streaming a response from your local model…', 'busy');
            if (shouldFollow) {
                scrollToLatestMessage();
            }
        } else if (streamEvent.type === 'status' && typeof streamEvent.content === 'string') {
            setStatus(streamEvent.content, 'busy');
        } else if (streamEvent.type === 'complete') {
            if (typeof streamEvent.rendered_html === 'string' && assistantMessage.content) {
                assistantMessage.content.innerHTML = streamEvent.rendered_html;
                assistantMessage.content.classList.remove('whitespace-pre-wrap');
            }

            finishAssistantMessage(assistantMessage, 'Saved locally');
            updateConversationTitle(streamEvent.conversation_title);
            updateMessageCount();
            setStatus('Response saved locally.', 'ready');
        } else if (streamEvent.type === 'error') {
            markAssistantError(
                assistantMessage,
                streamEvent.content || 'The local AI request failed.',
            );
            setStatus('The local model could not complete this response.', 'error');
        }
    };

    const streamResponse = async (action, formData, assistantMessage) => {
        const response = await fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                Accept: 'text/event-stream',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: activeRequest.signal,
        });

        if (!response.ok || !response.body) {
            const errors = {
                419: 'Your session expired. Refresh the page and sign in again.',
                422: 'Check the message and try again.',
                429: 'AI rate limit reached. Wait a minute and try again.',
            };

            throw new Error(errors[response.status] || 'The AI request was rejected.');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';
        let completed = false;

        while (true) {
            const { value, done } = await reader.read();
            buffer += decoder.decode(value ?? new Uint8Array(), { stream: !done });

            const blocks = buffer.split(/\r?\n\r?\n/);
            buffer = blocks.pop() ?? '';
            blocks.forEach((block) => {
                const streamEvent = parseSseEventBlock(block);
                if (streamEvent) {
                    handleStreamEvent(streamEvent, assistantMessage);
                    completed ||= streamEvent.type === 'complete';
                }
            });

            if (done) {
                if (buffer.trim()) {
                    const streamEvent = parseSseEventBlock(buffer);
                    if (streamEvent) {
                        handleStreamEvent(streamEvent, assistantMessage);
                        completed ||= streamEvent.type === 'complete';
                    }
                }
                break;
            }
        }

        return completed;
    };

    root.addEventListener('click', async (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const promptButton = event.target.closest('[data-ai-prompt]');
        if (promptButton && textarea) {
            textarea.value = promptButton.dataset.aiPrompt ?? '';
            resizeTextarea();
            textarea.focus();
            return;
        }

        const copyButton = event.target.closest('[data-ai-copy]');
        const messageContent = copyButton
            ?.closest('[data-ai-message-role]')
            ?.querySelector('[data-ai-message-content]')?.textContent;

        if (!copyButton || !messageContent) {
            return;
        }

        try {
            await navigator.clipboard.writeText(messageContent);
            copyButton.textContent = 'Copied';
            window.setTimeout(() => {
                copyButton.textContent = 'Copy response';
            }, 1600);
        } catch {
            copyButton.textContent = 'Copy unavailable';
        }
    });

    const conversationSearch = root.querySelector('[data-ai-conversation-search]');
    conversationSearch?.addEventListener('input', () => {
        const query = conversationSearch.value.trim().toLowerCase();
        const items = [...root.querySelectorAll('[data-ai-conversation-item]')];
        let visibleItems = 0;

        items.forEach((item) => {
            const matches = !query || item.dataset.conversationTitle?.includes(query);
            item.classList.toggle('hidden', !matches);
            visibleItems += matches ? 1 : 0;
        });

        root.querySelector('[data-ai-conversation-search-empty]')?.classList.toggle(
            'hidden',
            visibleItems > 0,
        );
    });

    const modeSelect = root.querySelector('[data-ai-mode-select]');
    modeSelect?.addEventListener('change', () => {
        const selectedOption = modeSelect.options[modeSelect.selectedIndex];
        const description = root.querySelector('[data-ai-mode-description]');

        if (description && selectedOption?.dataset.description) {
            description.textContent = selectedOption.dataset.description;
        }
    });

    textarea?.addEventListener('input', resizeTextarea);
    textarea?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            form?.requestSubmit();
        }
    });

    messages?.addEventListener('scroll', () => {
        const shouldShow = !isNearLatestMessage();
        scrollLatest?.classList.toggle('hidden', !shouldShow);
        scrollLatest?.classList.toggle('flex', shouldShow);
    });
    scrollLatest?.addEventListener('click', () => scrollToLatestMessage());

    const startAssistantRequest = async ({
        action,
        formData,
        userMessage = '',
        appendUserMessage = false,
        failedRequest = null,
    }) => {
        if ((appendUserMessage && !userMessage) || activeRequest) {
            return;
        }

        if (appendUserMessage) {
            appendMessage('[data-ai-user-template]', userMessage);
        }

        const assistantMessage = appendMessage('[data-ai-assistant-template]', '');

        if (!assistantMessage?.content) {
            return;
        }

        if (appendUserMessage && textarea) {
            textarea.value = '';
            resizeTextarea();
        }

        const retryButton = failedRequest?.querySelector('button');
        if (retryButton) {
            retryButton.disabled = true;
        }

        activeRequest = new AbortController();
        setBusy(true);
        setStatus(
            appendUserMessage
                ? 'Connecting to your local model…'
                : 'Retrying the previous message…',
            'busy',
        );

        try {
            const completed = await streamResponse(action, formData, assistantMessage);
            if (completed) {
                failedRequest?.remove();
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                if (!assistantMessage.content.textContent) {
                    assistantMessage.content.textContent = 'Response display stopped.';
                }
                finishAssistantMessage(assistantMessage, 'Stopped');
                setStatus('Streaming stopped in this browser.', 'ready');
            } else {
                markAssistantError(
                    assistantMessage,
                    error.message || 'The local AI request failed.',
                );
                setStatus('LM Studio request failed.', 'error');
            }
        } finally {
            activeRequest = undefined;
            setBusy(false);
            if (retryButton?.isConnected) {
                retryButton.disabled = false;
            }
            textarea.focus();
        }
    };

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const userMessage = String(formData.get('message') ?? '').trim();

        await startAssistantRequest({
            action: form.action,
            formData,
            userMessage,
            appendUserMessage: true,
        });
    });

    root.querySelectorAll('[data-ai-retry-form]').forEach((retryForm) => {
        retryForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const failedRequest = retryForm.closest('[data-ai-failed-request]');

            await startAssistantRequest({
                action: retryForm.action,
                formData: new FormData(retryForm),
                failedRequest,
            });
        });
    });

    const sidebarToggle = root.querySelector('[data-ai-sidebar-toggle]');
    const sidebar = root.querySelector('#ai-workspace-sidebar');
    const sidebarToggleIcon = root.querySelector('[data-ai-sidebar-toggle-icon]');
    sidebarToggle?.addEventListener('click', () => {
        const expanded = sidebarToggle.getAttribute('aria-expanded') === 'true';
        sidebarToggle.setAttribute('aria-expanded', String(!expanded));
        sidebar?.classList.toggle('hidden', expanded);
        sidebar?.classList.toggle('grid', !expanded);

        if (sidebarToggleIcon) {
            sidebarToggleIcon.textContent = expanded ? '＋' : '−';
        }
    });

    stop?.addEventListener('click', () => activeRequest?.abort());
    resizeTextarea();
    window.requestAnimationFrame(() => scrollToLatestMessage('auto'));
};
