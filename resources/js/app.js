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
