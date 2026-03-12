/**
 * Global form submit loader - disables buttons and shows loading state
 * to prevent double-clicks and improve UX during POST actions.
 */
const ORIGINAL_TEXT_ATTR = 'data-original-text';

function setButtonLoading(button, loading) {
    if (!button) return;

    if (loading) {
        if (button.tagName === 'BUTTON' && !button.querySelector('.action-spinner')) {
            const originalText = button.innerHTML?.trim() || button.textContent?.trim();
            if (originalText && !button.getAttribute(ORIGINAL_TEXT_ATTR)) {
                button.setAttribute(ORIGINAL_TEXT_ATTR, originalText);
            }
            const loadingText = button.getAttribute('data-loading-text') || 'Processing...';
            if (!button.querySelector('.form-loader-spinner')) {
                button.innerHTML = `<span class="form-loader-spinner"></span> ${loadingText}`;
            }
        } else if (button.querySelector('.action-spinner')) {
            button.classList.add('is-loading');
        }
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.classList.add('is-submit-loading');
    } else {
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.classList.remove('is-submit-loading');
        const original = button.getAttribute(ORIGINAL_TEXT_ATTR);
        if (original) {
            button.innerHTML = original;
            button.removeAttribute(ORIGINAL_TEXT_ATTR);
        }
    }
}

function initFormLoader() {
    document.addEventListener(
        'submit',
        (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;

            if (form.getAttribute('data-no-loader') === 'true') return;

            const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            if (buttons.length === 0) return;

            buttons.forEach((btn) => setButtonLoading(btn, true));
        },
        true
    );
}

export default { init: initFormLoader };
