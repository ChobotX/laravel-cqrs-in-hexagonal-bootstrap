export {};

const HIDDEN_CLASS = 'hidden';

function getDropdowns(): NodeListOf<HTMLElement> {
    return document.querySelectorAll<HTMLElement>('[data-dropdown]');
}

function getMenuItems(menu: HTMLElement): HTMLElement[] {
    return [...menu.querySelectorAll<HTMLElement>('[role="menuitem"]')];
}

function openDropdown(dropdown: HTMLElement): void {
    const menu = dropdown.querySelector<HTMLElement>('[data-dropdown-menu]');
    const toggle = dropdown.querySelector<HTMLElement>('[data-dropdown-toggle]');
    if (!menu || !toggle) {
        return;
    }

    menu.classList.remove(HIDDEN_CLASS);
    toggle.setAttribute('aria-expanded', 'true');

    const items = getMenuItems(menu);
    if (items.length > 0) {
        items[0].focus();
    }
}

function closeDropdown(dropdown: HTMLElement): void {
    const menu = dropdown.querySelector<HTMLElement>('[data-dropdown-menu]');
    const toggle = dropdown.querySelector<HTMLElement>('[data-dropdown-toggle]');
    if (!menu || !toggle) {
        return;
    }

    menu.classList.add(HIDDEN_CLASS);
    toggle.setAttribute('aria-expanded', 'false');
    toggle.focus();
}

function silentClose(dropdown: HTMLElement): void {
    const menu = dropdown.querySelector<HTMLElement>('[data-dropdown-menu]');
    const toggle = dropdown.querySelector<HTMLElement>('[data-dropdown-toggle]');
    if (!menu || !toggle) {
        return;
    }

    menu.classList.add(HIDDEN_CLASS);
    toggle.setAttribute('aria-expanded', 'false');
}

function isOpen(dropdown: HTMLElement): boolean {
    const menu = dropdown.querySelector<HTMLElement>('[data-dropdown-menu]');
    return menu !== null && !menu.classList.contains(HIDDEN_CLASS);
}

function handleToggleClick(dropdown: HTMLElement, target: Node): void {
    const toggle = dropdown.querySelector<HTMLElement>('[data-dropdown-toggle]');

    if (toggle?.contains(target)) {
        if (isOpen(dropdown)) {
            closeDropdown(dropdown);
        } else {
            openDropdown(dropdown);
        }
    } else if (!dropdown.contains(target) && isOpen(dropdown)) {
        silentClose(dropdown);
    }
}

function focusMenuItem(menu: HTMLElement, direction: number): void {
    const items = getMenuItems(menu);
    if (items.length === 0) {
        return;
    }

    const currentIndex = items.indexOf(document.activeElement as HTMLElement);
    const nextIndex = (currentIndex + direction + items.length) % items.length;
    items[nextIndex].focus();
}

function handleEscape(): void {
    for (const dropdown of getDropdowns()) {
        if (isOpen(dropdown)) {
            closeDropdown(dropdown);
        }
    }
}

function handleArrowKey(event: KeyboardEvent): void {
    const target = event.target as HTMLElement;
    const dropdown = target.closest<HTMLElement>('[data-dropdown]');
    if (!dropdown) {
        return;
    }

    const menu = dropdown.querySelector<HTMLElement>('[data-dropdown-menu]');
    if (!menu || !isOpen(dropdown)) {
        return;
    }

    event.preventDefault();
    const direction = event.key === 'ArrowDown' ? 1 : -1;
    focusMenuItem(menu, direction);
}

document.addEventListener('click', (event: MouseEvent): void => {
    const target = event.target as Node;

    for (const dropdown of getDropdowns()) {
        handleToggleClick(dropdown, target);
    }
});

document.addEventListener('keydown', (event: KeyboardEvent): void => {
    if (event.key === 'Escape') {
        handleEscape();
        return;
    }

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        handleArrowKey(event);
    }
});
