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

const auditUserModal = document.querySelector('[data-audit-user-modal]');

if (auditUserModal) {
    const openButton = document.querySelector('[data-audit-user-modal-open]');
    const closeButtons = auditUserModal.querySelectorAll('[data-audit-user-modal-close]');
    const searchInput = auditUserModal.querySelector('[data-audit-user-search]');
    const userOptions = [...auditUserModal.querySelectorAll('[data-audit-user-option]')];
    const userCheckboxes = [...auditUserModal.querySelectorAll('input[name="actor_ids[]"]')];
    const checkAllButton = auditUserModal.querySelector('[data-audit-user-check-all]');
    const clearButton = auditUserModal.querySelector('[data-audit-user-clear]');
    let initialSelection = new Set();

    const captureSelection = () => new Set(
        userCheckboxes
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.value),
    );

    const restoreSelection = () => {
        userCheckboxes.forEach((checkbox) => {
            checkbox.checked = initialSelection.has(checkbox.value);
        });
    };

    const filterOptions = () => {
        const query = (searchInput?.value ?? '').trim().toLocaleLowerCase();

        userOptions.forEach((option) => {
            option.hidden = query !== '' && ! option.dataset.auditUserName.includes(query);
        });
    };

    openButton?.addEventListener('click', () => {
        initialSelection = captureSelection();
        auditUserModal.showModal();
        searchInput?.focus();
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            restoreSelection();
            auditUserModal.close();
        });
    });

    auditUserModal.addEventListener('cancel', () => {
        restoreSelection();
    });

    searchInput?.addEventListener('input', filterOptions);

    checkAllButton?.addEventListener('click', () => {
        userOptions
            .filter((option) => ! option.hidden)
            .forEach((option) => {
                option.querySelector('input').checked = true;
            });
    });

    clearButton?.addEventListener('click', () => {
        userOptions
            .filter((option) => ! option.hidden)
            .forEach((option) => {
                option.querySelector('input').checked = false;
            });
    });
}
