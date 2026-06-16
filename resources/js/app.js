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
    const roleFilterButtons = [...auditUserModal.querySelectorAll('[data-audit-user-role-filter]')];
    const selectedCount = auditUserModal.querySelector('[data-audit-user-selected-count]');
    const visibleCount = auditUserModal.querySelector('[data-audit-user-visible-count]');
    const selectionText = auditUserModal.querySelector('[data-audit-user-selection-text]');
    const noResults = auditUserModal.querySelector('[data-audit-user-no-results]');
    let initialSelection = new Set();
    let activeRoleFilter = 'all';

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

    const updateSelectionState = () => {
        const checkedCount = userCheckboxes.filter((checkbox) => checkbox.checked).length;

        if (selectedCount) {
            selectedCount.textContent = checkedCount.toString();
        }

        if (selectionText) {
            selectionText.textContent = checkedCount === 0
                ? 'Sin usuarios marcados: se mostraran todos los eventos.'
                : `Se mostraran eventos de ${checkedCount} usuario${checkedCount === 1 ? '' : 's'} marcado${checkedCount === 1 ? '' : 's'}.`;
        }
    };

    const filterOptions = () => {
        const query = (searchInput?.value ?? '').trim().toLocaleLowerCase();
        let visibleOptions = 0;

        userOptions.forEach((option) => {
            const checkbox = option.querySelector('input');
            const matchesQuery = query === '' || option.dataset.auditUserName.includes(query);
            const matchesRole = activeRoleFilter === 'all'
                || option.dataset.auditUserRole === activeRoleFilter
                || (activeRoleFilter === 'selected' && checkbox.checked);
            const isVisible = matchesQuery && matchesRole;

            option.hidden = ! isVisible;
            visibleOptions += isVisible ? 1 : 0;
        });

        if (visibleCount) {
            visibleCount.textContent = visibleOptions.toString();
        }

        if (noResults) {
            noResults.hidden = visibleOptions > 0 || userOptions.length === 0;
        }
    };

    openButton?.addEventListener('click', () => {
        initialSelection = captureSelection();
        activeRoleFilter = 'all';
        roleFilterButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.auditUserRoleFilter === activeRoleFilter);
        });
        if (searchInput) {
            searchInput.value = '';
        }
        updateSelectionState();
        filterOptions();
        auditUserModal.showModal();
        searchInput?.focus();
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            restoreSelection();
            updateSelectionState();
            filterOptions();
            auditUserModal.close();
        });
    });

    auditUserModal.addEventListener('cancel', () => {
        restoreSelection();
        updateSelectionState();
        filterOptions();
    });

    searchInput?.addEventListener('input', filterOptions);

    roleFilterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeRoleFilter = button.dataset.auditUserRoleFilter;
            roleFilterButtons.forEach((roleButton) => {
                roleButton.classList.toggle('is-active', roleButton === button);
            });
            filterOptions();
        });
    });

    userCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            updateSelectionState();
            if (activeRoleFilter === 'selected') {
                filterOptions();
            }
        });
    });

    checkAllButton?.addEventListener('click', () => {
        userOptions
            .filter((option) => ! option.hidden)
            .forEach((option) => {
                option.querySelector('input').checked = true;
            });
        updateSelectionState();
        filterOptions();
    });

    clearButton?.addEventListener('click', () => {
        userCheckboxes.forEach((checkbox) => {
            checkbox.checked = false;
        });
        updateSelectionState();
        filterOptions();
    });

    updateSelectionState();
    filterOptions();
}
