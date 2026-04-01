import { calculateCoordinates, isValidPosition, type Position, resolvePosition } from './tooltip-position';

const TOOLTIP_ID = 'app-tooltip';
const OFF_SCREEN = -9999;

let currentTrigger: HTMLElement | null = null;

function getOrCreateTooltip(): HTMLElement {
    const existing = document.getElementById(TOOLTIP_ID);
    if (existing) {
        return existing;
    }

    const el = document.createElement('div');
    el.id = TOOLTIP_ID;
    el.className = 'tooltip';
    el.setAttribute('role', 'tooltip');
    document.body.appendChild(el);
    return el;
}

function getContent(trigger: HTMLElement): { html: boolean; content: string } | null {
    const template = trigger.querySelector<HTMLTemplateElement>(':scope > template[data-tooltip-content]');
    if (template) {
        return { html: true, content: template.innerHTML };
    }

    const text = trigger.getAttribute('data-tooltip');
    if (text) {
        return { html: false, content: text };
    }

    return null;
}

function getPreferredPosition(trigger: HTMLElement): Position {
    const attr = trigger.getAttribute('data-tooltip-position');
    if (isValidPosition(attr)) {
        return attr;
    }
    return 'top';
}

function showTooltip(trigger: HTMLElement): void {
    if (currentTrigger === trigger) {
        return;
    }

    const result = getContent(trigger);
    if (!result) {
        return;
    }

    currentTrigger = trigger;
    const tooltip = getOrCreateTooltip();

    if (result.html) {
        tooltip.innerHTML = result.content;
    } else {
        tooltip.textContent = result.content;
    }

    tooltip.style.top = `${OFF_SCREEN}px`;
    tooltip.style.left = `${OFF_SCREEN}px`;
    tooltip.classList.remove('tooltip--top', 'tooltip--bottom', 'tooltip--left', 'tooltip--right');
    tooltip.classList.add('tooltip--visible');

    const triggerRect = trigger.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    const preferred = getPreferredPosition(trigger);
    const position = resolvePosition(preferred, triggerRect, tooltipRect);
    const { top, left } = calculateCoordinates(position, triggerRect, tooltipRect);

    tooltip.classList.add(`tooltip--${position}`);
    tooltip.style.top = `${top}px`;
    tooltip.style.left = `${left}px`;
}

function hideTooltip(): void {
    if (!currentTrigger) {
        return;
    }

    currentTrigger = null;
    const tooltip = document.getElementById(TOOLTIP_ID);
    if (tooltip) {
        tooltip.classList.remove(
            'tooltip--visible',
            'tooltip--top',
            'tooltip--bottom',
            'tooltip--left',
            'tooltip--right',
        );
    }
}

function findTrigger(target: EventTarget | null): HTMLElement | null {
    if (!(target instanceof Element)) {
        return null;
    }

    return target.closest<HTMLElement>('[data-tooltip]');
}

document.addEventListener('mouseover', (event: MouseEvent): void => {
    const trigger = findTrigger(event.target);
    if (trigger) {
        showTooltip(trigger);
    }
});

document.addEventListener('mouseout', (event: MouseEvent): void => {
    const trigger = findTrigger(event.target);
    if (!trigger) {
        return;
    }
    const related = event.relatedTarget;
    if (related instanceof Node && trigger.contains(related)) {
        return;
    }
    hideTooltip();
});

document.addEventListener('focusin', (event: FocusEvent): void => {
    const trigger = findTrigger(event.target);
    if (trigger) {
        showTooltip(trigger);
    }
});

document.addEventListener('focusout', (event: FocusEvent): void => {
    const trigger = findTrigger(event.target);
    if (!trigger) {
        return;
    }
    const related = event.relatedTarget;
    if (related instanceof Node && trigger.contains(related)) {
        return;
    }
    hideTooltip();
});

document.addEventListener('keydown', (event: KeyboardEvent): void => {
    if (event.key === 'Escape') {
        hideTooltip();
    }
});

document.addEventListener(
    'scroll',
    (): void => {
        hideTooltip();
    },
    { capture: true, passive: true },
);
