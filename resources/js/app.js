document.addEventListener('click', (event) => {
    document.querySelectorAll('details[data-account-menu][open]').forEach((menu) => {
        if (!menu.contains(event.target)) {
            menu.removeAttribute('open');
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    document.querySelectorAll('details[data-account-menu][open]').forEach((menu) => {
        menu.removeAttribute('open');
    });
});

document.querySelectorAll('[data-code-tab]').forEach((tab) => {
    tab.addEventListener('click', () => {
        const selectedPanel = tab.dataset.codeTab;

        document.querySelectorAll('[data-code-tab]').forEach((button) => {
            button.setAttribute('aria-selected', String(button === tab));
        });

        document.querySelectorAll('[data-code-panel]').forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.codePanel !== selectedPanel);
        });
    });
});

document.querySelectorAll('[data-email-action]').forEach((button) => {
    button.addEventListener('click', () => {
        const widget = button.closest('[data-protected-email]');

        if (!widget?.dataset.emailLocal || !widget.dataset.emailDomain) {
            return;
        }

        const email = `${widget.dataset.emailLocal}@${widget.dataset.emailDomain}`;
        const display = widget.querySelector('[data-email-display]');

        if (display) {
            display.textContent = email;
        }

        if (button.dataset.emailAction === 'send') {
            window.location.href = `mailto:${email}`;
        }
    });
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.dataset.confirm;

        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });
});

const aiChat = document.querySelector('[data-ai-chat]');

if (aiChat) {
    const form = aiChat.querySelector('[data-ai-message-form]');
    const messages = aiChat.querySelector('[data-ai-messages]');
    const submit = aiChat.querySelector('[data-ai-submit]');
    const stop = aiChat.querySelector('[data-ai-stop]');
    const status = aiChat.querySelector('[data-ai-status]');
    let activeRequest;

    const appendMessage = (templateSelector, content) => {
        const template = aiChat.querySelector(templateSelector);

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
        const appended = messages.lastElementChild;
        appended?.scrollIntoView({ behavior: 'smooth', block: 'end' });

        return appended?.querySelector('[data-ai-message-content]') ?? null;
    };

    const setBusy = (busy) => {
        if (submit) {
            submit.disabled = busy;
            submit.textContent = busy ? 'Thinking…' : 'Send';
        }

        stop?.classList.toggle('hidden', !busy);
    };

    const handleEventBlock = (block, assistantContent) => {
        const data = block
            .split(/\r?\n/)
            .filter((line) => line.startsWith('data:'))
            .map((line) => line.slice(5).trimStart())
            .join('\n');

        if (!data) {
            return;
        }

        const event = JSON.parse(data);

        if (event.type === 'delta' && typeof event.content === 'string') {
            assistantContent.textContent += event.content;
            assistantContent
                .closest('article')
                ?.scrollIntoView({ behavior: 'smooth', block: 'end' });
        } else if (event.type === 'status' && status) {
            status.textContent = event.content;
        } else if (event.type === 'complete') {
            if (status) {
                status.textContent = 'Response saved locally.';
            }

            const title = aiChat.querySelector('[data-ai-conversation-title]');
            if (title && typeof event.conversation_title === 'string') {
                title.textContent = event.conversation_title;
            }
        } else if (event.type === 'error') {
            assistantContent.textContent = event.content || 'The local AI request failed.';
            assistantContent
                .closest('pre')
                ?.classList.add('border-orange-300', 'bg-orange-50', 'text-orange-800');
        }
    };

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const userMessage = String(formData.get('message') ?? '').trim();
        if (!userMessage || activeRequest) {
            return;
        }

        appendMessage('[data-ai-user-template]', userMessage);
        const assistantContent = appendMessage('[data-ai-assistant-template]', '');
        const textarea = form.querySelector('textarea[name="message"]');
        if (textarea) {
            textarea.value = '';
        }

        if (!assistantContent) {
            return;
        }

        activeRequest = new AbortController();
        setBusy(true);
        if (status) {
            status.textContent = 'Connecting to local LM Studio…';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    Accept: 'text/event-stream',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeRequest.signal,
            });

            if (!response.ok || !response.body) {
                throw new Error(
                    response.status === 429
                        ? 'AI rate limit reached. Wait a minute and try again.'
                        : 'The AI request was rejected.',
                );
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { value, done } = await reader.read();
                buffer += decoder.decode(value ?? new Uint8Array(), { stream: !done });

                const blocks = buffer.split(/\r?\n\r?\n/);
                buffer = blocks.pop() ?? '';
                blocks.forEach((block) => handleEventBlock(block, assistantContent));

                if (done) {
                    if (buffer.trim()) {
                        handleEventBlock(buffer, assistantContent);
                    }
                    break;
                }
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                if (status) {
                    status.textContent = 'Streaming stopped in this browser.';
                }
            } else {
                assistantContent.textContent = error.message || 'The local AI request failed.';
                assistantContent.classList.add(
                    'border-orange-300',
                    'bg-orange-50',
                    'text-orange-800',
                );
                if (status) {
                    status.textContent = 'LM Studio request failed.';
                }
            }
        } finally {
            activeRequest = undefined;
            setBusy(false);
        }
    });

    stop?.addEventListener('click', () => activeRequest?.abort());
}
