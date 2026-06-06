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

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');

    if (! button) {
        return;
    }

    const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';

    applyTheme(nextTheme);
});
