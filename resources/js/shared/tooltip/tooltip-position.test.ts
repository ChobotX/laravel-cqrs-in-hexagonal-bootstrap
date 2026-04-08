import { beforeEach, describe, expect, it, vi } from 'vitest';
import { ARROW_OFFSET, calculateCoordinates, isValidPosition, resolvePosition } from './tooltip-position';

function makeRect(overrides: Partial<DOMRect> = {}): DOMRect {
    return {
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        width: 0,
        height: 0,
        x: 0,
        y: 0,
        toJSON: () => ({}),
        ...overrides,
    };
}

describe('isValidPosition', () => {
    it('returns true for valid positions', () => {
        for (const p of ['top', 'bottom', 'left', 'right', 'top-left', 'top-right', 'bottom-left', 'bottom-right']) {
            expect(isValidPosition(p)).toBe(true);
        }
    });

    it('returns false for invalid values', () => {
        expect(isValidPosition('center')).toBe(false);
        expect(isValidPosition(null)).toBe(false);
        expect(isValidPosition('')).toBe(false);
    });
});

describe('resolvePosition', () => {
    beforeEach(() => {
        vi.stubGlobal('innerWidth', 1024);
        vi.stubGlobal('innerHeight', 768);
    });

    it('keeps preferred when space is available', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('top');
    });

    it('flips top to bottom when hitting top edge', () => {
        const trigger = makeRect({ top: 20, bottom: 52, left: 400, right: 500 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('bottom');
    });

    it('flips bottom to top when hitting bottom edge', () => {
        const trigger = makeRect({ top: 700, bottom: 732, left: 400, right: 500 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('bottom', trigger, tooltip)).toBe('top');
    });

    it('flips left to right when hitting left edge', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 20, right: 120 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('left', trigger, tooltip)).toBe('right');
    });

    it('flips right to left when hitting right edge', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 900, right: 1000 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('right', trigger, tooltip)).toBe('left');
    });

    it('uses bottom-left for top-right corner', () => {
        const trigger = makeRect({ top: 20, bottom: 56, left: 950, right: 986, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('bottom-left');
    });

    it('uses top-left for bottom-right corner', () => {
        const trigger = makeRect({ top: 700, bottom: 736, left: 950, right: 986, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('top-left');
    });

    it('uses bottom-right for top-left corner', () => {
        const trigger = makeRect({ top: 20, bottom: 56, left: 20, right: 56, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('bottom-right');
    });

    it('uses top-right for bottom-left corner', () => {
        const trigger = makeRect({ top: 700, bottom: 736, left: 20, right: 56, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('top-right');
    });

    it('does not trigger horizontal edge for centered top/bottom when trigger is near edge', () => {
        const trigger = makeRect({ top: 200, bottom: 236, left: 900, right: 936, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('top');
    });

    it('uses left when only hitting right edge', () => {
        const trigger = makeRect({ top: 200, bottom: 236, left: 950, right: 986, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('left');
    });

    it('uses right when only hitting left edge', () => {
        const trigger = makeRect({ top: 200, bottom: 236, left: 20, right: 56, width: 36, height: 36 });
        const tooltip = makeRect({ width: 70, height: 24 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('right');
    });
});

describe('calculateCoordinates', () => {
    beforeEach(() => {
        vi.stubGlobal('scrollX', 0);
        vi.stubGlobal('scrollY', 0);
    });

    it('centers above trigger for top', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left, arrowLeft } = calculateCoordinates('top', trigger, tooltip);

        expect(top).toBe(200 - 30 - ARROW_OFFSET);
        expect(left).toBe(210);
        expect(arrowLeft).toBe(40);
    });

    it('centers below trigger for bottom', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left, arrowLeft } = calculateCoordinates('bottom', trigger, tooltip);

        expect(top).toBe(232 + ARROW_OFFSET);
        expect(left).toBe(210);
        expect(arrowLeft).toBe(40);
    });

    it('centers left of trigger for left', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left } = calculateCoordinates('left', trigger, tooltip);

        expect(top).toBe(201);
        expect(left).toBe(200 - 80 - ARROW_OFFSET);
    });

    it('centers right of trigger for right', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left } = calculateCoordinates('right', trigger, tooltip);

        expect(top).toBe(201);
        expect(left).toBe(300 + ARROW_OFFSET);
    });

    it('top-left: right edge of tooltip aligns with right edge of trigger, arrow at center', () => {
        const trigger = makeRect({ top: 200, bottom: 244, left: 900, right: 944, width: 44, height: 44 });
        const tooltip = makeRect({ width: 80, height: 24 });
        const { top, left, arrowLeft } = calculateCoordinates('top-left', trigger, tooltip);

        expect(top).toBe(200 - 24 - ARROW_OFFSET);
        expect(left).toBe(944 - 80);
        expect(arrowLeft).toBe(900 + 22 - (944 - 80));
    });

    it('top-right: left edge of tooltip aligns with left edge of trigger, arrow at center', () => {
        const trigger = makeRect({ top: 200, bottom: 244, left: 60, right: 104, width: 44, height: 44 });
        const tooltip = makeRect({ width: 80, height: 24 });
        const { top, left, arrowLeft } = calculateCoordinates('top-right', trigger, tooltip);

        expect(top).toBe(200 - 24 - ARROW_OFFSET);
        expect(left).toBe(60);
        expect(arrowLeft).toBe(22);
    });

    it('bottom-left: right edge aligns with trigger right, arrow at center', () => {
        const trigger = makeRect({ top: 200, bottom: 244, left: 900, right: 944, width: 44, height: 44 });
        const tooltip = makeRect({ width: 80, height: 24 });
        const { top, left, arrowLeft } = calculateCoordinates('bottom-left', trigger, tooltip);

        expect(top).toBe(244 + ARROW_OFFSET);
        expect(left).toBe(944 - 80);
        expect(arrowLeft).toBe(900 + 22 - (944 - 80));
    });

    it('bottom-right: left edge aligns with trigger left, arrow at center', () => {
        const trigger = makeRect({ top: 200, bottom: 244, left: 60, right: 104, width: 44, height: 44 });
        const tooltip = makeRect({ width: 80, height: 24 });
        const { top, left, arrowLeft } = calculateCoordinates('bottom-right', trigger, tooltip);

        expect(top).toBe(244 + ARROW_OFFSET);
        expect(left).toBe(60);
        expect(arrowLeft).toBe(22);
    });
});
