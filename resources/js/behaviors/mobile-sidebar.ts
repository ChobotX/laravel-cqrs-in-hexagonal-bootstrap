import { htmlElementFromEventTarget } from '../core/dom/event-target-guards';

const HIDDEN_CLASS = 'hidden';

function getOverlay(): HTMLElement | null {
    return document.getElementById('mobile-sidebar-overlay');
}

function openSidebar(): void {
    const overlay = getOverlay();
    if (!overlay) {
        return;
    }

    overlay.classList.remove(HIDDEN_CLASS);
    document.body.style.overflow = 'hidden';

    const closeButton = overlay.querySelector<HTMLElement>('[data-sidebar-close]');
    if (closeButton) {
        closeButton.focus();
    }
}

function closeSidebar(): void {
    const overlay = getOverlay();
    if (!overlay) {
        return;
    }

    overlay.classList.add(HIDDEN_CLASS);
    document.body.style.overflow = '';

    const openButton = document.querySelector<HTMLElement>('[data-sidebar-open]');
    if (openButton) {
        openButton.focus();
    }
}

function isSidebarOpen(): boolean {
    const overlay = getOverlay();
    return overlay !== null && !overlay.classList.contains(HIDDEN_CLASS);
}

document.addEventListener('click', (event: MouseEvent): void => {
    const target = htmlElementFromEventTarget(event.target);
    if (target === null) {
        return;
    }

    if (target.closest('[data-sidebar-open]')) {
        openSidebar();
        return;
    }

    if (target.closest('[data-sidebar-close]') || target.closest('[data-sidebar-backdrop]')) {
        closeSidebar();
        return;
    }

    const overlay = getOverlay();
    if (overlay && isSidebarOpen() && target.closest('#mobile-sidebar-overlay a')) {
        closeSidebar();
    }
});

document.addEventListener('keydown', (event: KeyboardEvent): void => {
    if (event.key === 'Escape' && isSidebarOpen()) {
        closeSidebar();
    }
});
