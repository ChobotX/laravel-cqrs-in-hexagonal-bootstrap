for (const field of document.querySelectorAll<HTMLElement>('[data-avatar-field]')) {
    const removeBtn = field.querySelector<HTMLButtonElement>('[data-avatar-remove-btn]');
    const preview = field.querySelector<HTMLElement>('[data-avatar-preview]');
    const removeInput = field.querySelector<HTMLInputElement>('[data-avatar-remove]');

    if (!removeBtn || !preview || !removeInput) {
        continue;
    }

    removeBtn.addEventListener('click', () => {
        preview.remove();
        removeInput.value = '1';
    });
}
