import { beforeEach, describe, expect, it, vi } from 'vitest';
import { calculateCoordinates, isValidPosition, resolvePosition } from './tooltip-position';

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
        expect(isValidPosition('top')).toBe(true);
        expect(isValidPosition('bottom')).toBe(true);
        expect(isValidPosition('left')).toBe(true);
        expect(isValidPosition('right')).toBe(true);
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

    it('keeps preferred position when space is available', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('top');
        expect(resolvePosition('bottom', trigger, tooltip)).toBe('bottom');
        expect(resolvePosition('left', trigger, tooltip)).toBe('left');
        expect(resolvePosition('right', trigger, tooltip)).toBe('right');
    });

    it('flips top to bottom when hitting top edge', () => {
        const trigger = makeRect({ top: 20, bottom: 52 });
        const tooltip = makeRect({ height: 30 });

        expect(resolvePosition('top', trigger, tooltip)).toBe('bottom');
    });

    it('flips bottom to top when hitting bottom edge', () => {
        const trigger = makeRect({ top: 700, bottom: 732 });
        const tooltip = makeRect({ height: 30 });

        expect(resolvePosition('bottom', trigger, tooltip)).toBe('top');
    });

    it('flips left to right when hitting left edge', () => {
        const trigger = makeRect({ left: 20, right: 120 });
        const tooltip = makeRect({ width: 80 });

        expect(resolvePosition('left', trigger, tooltip)).toBe('right');
    });

    it('flips right to left when hitting right edge', () => {
        const trigger = makeRect({ left: 900, right: 1000 });
        const tooltip = makeRect({ width: 80 });

        expect(resolvePosition('right', trigger, tooltip)).toBe('left');
    });

    it('does not flip when preferred is not the constrained edge', () => {
        const trigger = makeRect({ top: 20, bottom: 52, left: 200, right: 300 });
        const tooltip = makeRect({ width: 80, height: 30 });

        expect(resolvePosition('left', trigger, tooltip)).toBe('left');
        expect(resolvePosition('right', trigger, tooltip)).toBe('right');
    });
});

describe('calculateCoordinates', () => {
    beforeEach(() => {
        vi.stubGlobal('innerWidth', 1024);
        vi.stubGlobal('innerHeight', 768);
    });

    it('positions above trigger for top', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left } = calculateCoordinates('top', trigger, tooltip);

        expect(top).toBe(162);
        expect(left).toBe(210);
    });

    it('positions below trigger for bottom', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left } = calculateCoordinates('bottom', trigger, tooltip);

        expect(top).toBe(240);
        expect(left).toBe(210);
    });

    it('positions to left of trigger', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left } = calculateCoordinates('left', trigger, tooltip);

        expect(top).toBe(201);
        expect(left).toBe(112);
    });

    it('positions to right of trigger', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 30 });
        const { top, left } = calculateCoordinates('right', trigger, tooltip);

        expect(top).toBe(201);
        expect(left).toBe(308);
    });

    it('clamps left edge to viewport margin', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 10, right: 50, width: 40, height: 32 });
        const tooltip = makeRect({ width: 200, height: 30 });
        const { left } = calculateCoordinates('top', trigger, tooltip);

        expect(left).toBe(50);
    });

    it('clamps right edge to viewport margin', () => {
        const trigger = makeRect({ top: 200, bottom: 232, left: 950, right: 1020, width: 70, height: 32 });
        const tooltip = makeRect({ width: 200, height: 30 });
        const { left } = calculateCoordinates('top', trigger, tooltip);

        expect(left).toBe(774);
    });

    it('clamps top edge to viewport margin', () => {
        const trigger = makeRect({ top: 10, bottom: 42, left: 200, right: 300, width: 100, height: 32 });
        const tooltip = makeRect({ width: 80, height: 100 });
        const { top } = calculateCoordinates('top', trigger, tooltip);

        expect(top).toBe(50);
    });
});
