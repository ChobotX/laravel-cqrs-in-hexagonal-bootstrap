export type Position = 'top' | 'bottom' | 'left' | 'right' | 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';

export const EDGE_MARGIN = 50;
export const ARROW_OFFSET = 10;

const VALID_POSITIONS: Set<string> = new Set([
    'top',
    'bottom',
    'left',
    'right',
    'top-left',
    'top-right',
    'bottom-left',
    'bottom-right',
]);

export function isValidPosition(value: string | null): value is Position {
    return typeof value === 'string' && VALID_POSITIONS.has(value);
}

export function resolvePosition(preferred: Position, triggerRect: DOMRect, tooltipRect: DOMRect): Position {
    const centerX = triggerRect.left + triggerRect.width / 2;

    const hitsRight =
        preferred === 'left' || preferred === 'right'
            ? triggerRect.right + tooltipRect.width + ARROW_OFFSET > window.innerWidth - EDGE_MARGIN
            : centerX + tooltipRect.width / 2 > window.innerWidth - EDGE_MARGIN;

    const hitsLeft =
        preferred === 'left' || preferred === 'right'
            ? triggerRect.left - tooltipRect.width - ARROW_OFFSET < EDGE_MARGIN
            : centerX - tooltipRect.width / 2 < EDGE_MARGIN;

    const hitsTop = triggerRect.top - tooltipRect.height - ARROW_OFFSET < EDGE_MARGIN;
    const hitsBottom = triggerRect.bottom + tooltipRect.height + ARROW_OFFSET > window.innerHeight - EDGE_MARGIN;

    const key = `${hitsRight},${hitsLeft},${hitsTop},${hitsBottom}`;

    const fallback: Record<string, Position> = {
        'true,false,true,false': 'bottom-left',
        'true,false,false,true': 'top-left',
        'false,true,true,false': 'bottom-right',
        'false,true,false,true': 'top-right',
        'true,false,false,false': 'left',
        'false,true,false,false': 'right',
        'false,false,true,false': 'bottom',
        'false,false,false,true': 'top',
    };

    return fallback[key] ?? preferred;
}

export interface TooltipCoordinates {
    top: number;
    left: number;
    arrowLeft: number;
}

export function calculateCoordinates(
    position: Position,
    triggerRect: DOMRect,
    tooltipRect: DOMRect,
): TooltipCoordinates {
    const { top, left, right, bottom, width: aw, height: ah } = triggerRect;
    const sx = window.scrollX;
    const sy = window.scrollY;
    const tw = tooltipRect.width;
    const th = tooltipRect.height;
    const centerX = sx + left + aw / 2;
    const centerY = sy + top + ah / 2;
    const centeredLeft = centerX - tw / 2;

    switch (position) {
        case 'top':
            return { top: sy + top - th - ARROW_OFFSET, left: centeredLeft, arrowLeft: tw / 2 };
        case 'bottom':
            return { top: sy + bottom + ARROW_OFFSET, left: centeredLeft, arrowLeft: tw / 2 };
        case 'left':
            return { top: centerY - th / 2, left: sx + left - tw - ARROW_OFFSET, arrowLeft: tw / 2 };
        case 'right':
            return { top: centerY - th / 2, left: sx + right + ARROW_OFFSET, arrowLeft: tw / 2 };
        case 'top-left': {
            const l = sx + right - tw;
            return { top: sy + top - th - ARROW_OFFSET, left: l, arrowLeft: centerX - l };
        }
        case 'top-right': {
            const l = sx + left;
            return { top: sy + top - th - ARROW_OFFSET, left: l, arrowLeft: centerX - l };
        }
        case 'bottom-left': {
            const l = sx + right - tw;
            return { top: sy + bottom + ARROW_OFFSET, left: l, arrowLeft: centerX - l };
        }
        case 'bottom-right': {
            const l = sx + left;
            return { top: sy + bottom + ARROW_OFFSET, left: l, arrowLeft: centerX - l };
        }
    }
}
