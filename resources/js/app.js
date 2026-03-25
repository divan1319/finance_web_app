import './bootstrap';

function initMobileNav() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const panel = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('mobile-menu-icon-open');
    const iconClose = document.getElementById('mobile-menu-icon-close');

    if (!toggle || !panel || !iconOpen || !iconClose) {
        return;
    }

    const srOnly = toggle.querySelector('.sr-only');

    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (srOnly) {
            srOnly.textContent = open ? 'Cerrar menú principal' : 'Abrir menú principal';
        }
        panel.classList.toggle('hidden', !open);
        panel.classList.toggle('block', open);
        iconOpen.classList.toggle('hidden', open);
        iconClose.classList.toggle('hidden', !open);
    };

    toggle.addEventListener('click', () => {
        setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            toggle.focus();
        }
    });
}

document.addEventListener('DOMContentLoaded', initMobileNav);
