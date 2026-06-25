import './bootstrap';

const applyTheme = (theme) => {
    const resolvedTheme = theme === 'dark' ? 'dark' : 'light';

    document.documentElement.classList.toggle('dark', resolvedTheme === 'dark');
    document.documentElement.dataset.theme = resolvedTheme;
    localStorage.setItem('theme', resolvedTheme);
};

const storedTheme = localStorage.getItem('theme');
const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

applyTheme(storedTheme ?? preferredTheme);

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const transitionDelay = prefersReducedMotion ? 0 : 140;
let navigationStarted = false;

const showLoadingState = () => {
    if (navigationStarted) {
        return;
    }

    navigationStarted = true;
    document.documentElement.classList.add('is-leaving', 'is-loading');
    document.querySelector('[data-loading-screen]')?.setAttribute('aria-hidden', 'false');
};

const hideLoadingState = () => {
    navigationStarted = false;
    document.documentElement.classList.remove('is-leaving', 'is-loading');
    document.querySelector('[data-loading-screen]')?.setAttribute('aria-hidden', 'true');
};

const shouldAnimateLink = (link, event) => {
    if (! link || event.defaultPrevented || event.button !== 0) {
        return false;
    }

    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return false;
    }

    if (link.target && link.target !== '_self') {
        return false;
    }

    if (link.hasAttribute('download')) {
        return false;
    }

    const destination = new URL(link.href, window.location.href);

    if (destination.origin !== window.location.origin) {
        return false;
    }

    if (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash) {
        return false;
    }

    return destination.href !== window.location.href;
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');

    if (! button) {
        return;
    }

    const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';

    applyTheme(nextTheme);
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');

    if (! shouldAnimateLink(link, event)) {
        return;
    }

    event.preventDefault();
    showLoadingState();

    window.setTimeout(() => {
        window.location.href = link.href;
    }, transitionDelay);
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (! form.matches('form')) {
        return;
    }

    showLoadingState();
});

window.addEventListener('pageshow', hideLoadingState);
