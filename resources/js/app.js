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

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');

    if (! button) {
        return;
    }

    const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';

    applyTheme(nextTheme);
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (! form.matches('form[data-auth-loading]')) {
        return;
    }

    showLoadingState();
});

window.addEventListener('pageshow', hideLoadingState);
