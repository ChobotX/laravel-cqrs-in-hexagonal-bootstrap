document.addEventListener('DOMContentLoaded', () => {
    for (const toggle of document.querySelectorAll<HTMLInputElement>('[data-module-toggle]')) {
        const moduleSlug = toggle.dataset.moduleToggle;
        const checkboxes = document.querySelectorAll<HTMLInputElement>(`input[data-module="${moduleSlug}"]`);

        function updateToggleState(): void {
            const checked = Array.from(checkboxes).filter((cb) => cb.checked);
            toggle.checked = checked.length === checkboxes.length && checkboxes.length > 0;
            toggle.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }

        toggle.addEventListener('change', () => {
            for (const cb of checkboxes) {
                cb.checked = toggle.checked;
            }
        });

        for (const cb of checkboxes) {
            cb.addEventListener('change', updateToggleState);
        }

        updateToggleState();
    }
});
