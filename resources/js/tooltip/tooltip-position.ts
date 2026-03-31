export type Position = 'top' | 'bottom' | 'left' | 'right';

export const VIEWPORT_MARGIN = 50;
export const OFFSET = 8;

const FLIP: Record<Position, Position> = { top: 'bottom', bottom: 'top', left: 'right', right: 'left' };

export function isValidPosition(value: string | null): value is Position {
    return value === 'top' || value === 'bottom' || value === 'left' || value === 'right';
}

export function resolvePosition(preferred: Position, triggerRect: DOMRect, tooltipRect: DOMRect): Position {
    const hitsEdge: Record<Position, boolean> = {
        top: triggerRect.top - tooltipRect.height - OFFSET < VIEWPORT_MARGIN,
        bottom: triggerRect.bottom + tooltipRect.height + OFFSET > window.innerHeight - VIEWPORT_MARGIN,
        left: triggerRect.left - tooltipRect.width - OFFSET < VIEWPORT_MARGIN,
        right: triggerRect.right + tooltipRect.width + OFFSET > window.innerWidth - VIEWPORT_MARGIN,
    };

    if (hitsEdge[preferred]) {
        return FLIP[preferred];
    }

    return preferred;
}

export function calculateCoordinates(
    position: Position,
    triggerRect: DOMRect,
    tooltipRect: DOMRect,
): { top: number; left: number } {
    let top: number;
    let left: number;

    switch (position) {
        case 'top':
            top = triggerRect.top - tooltipRect.height - OFFSET;
            left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2;
            break;
        case 'bottom':
            top = triggerRect.bottom + OFFSET;
            left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2;
            break;
        case 'left':
            top = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2;
            left = triggerRect.left - tooltipRect.width - OFFSET;
            break;
        case 'right':
            top = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2;
            left = triggerRect.right + OFFSET;
            break;
    }

    left = Math.max(VIEWPORT_MARGIN, Math.min(left, window.innerWidth - tooltipRect.width - VIEWPORT_MARGIN));
    top = Math.max(VIEWPORT_MARGIN, Math.min(top, window.innerHeight - tooltipRect.height - VIEWPORT_MARGIN));

    return { top, left };
}
