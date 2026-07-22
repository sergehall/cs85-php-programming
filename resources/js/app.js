import { initAiChat } from './ai-chat';

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

const contactDetailsForm = document.querySelector('[data-contact-details-form]');

if (contactDetailsForm) {
    const contactSelect = contactDetailsForm.querySelector('[data-contact-details-select]');
    const phoneInput = contactDetailsForm.querySelector('[data-contact-details-phone]');
    const companyInput = contactDetailsForm.querySelector('[data-contact-details-company]');
    const groupSelect = contactDetailsForm.querySelector('[data-contact-details-group]');

    if (contactSelect && phoneInput && companyInput && groupSelect) {
        contactSelect.addEventListener('change', () => {
            const option = contactSelect.options[contactSelect.selectedIndex];

            if (!option?.value) {
                phoneInput.value = '';
                companyInput.value = '';
                groupSelect.value = '';
                return;
            }

            phoneInput.value = option.dataset.phone ?? '';
            companyInput.value = option.dataset.company ?? '';
            groupSelect.value = option.dataset.groupId ?? '';
        });
    }
}

initAiChat();
