import raw from '../../../shared/ui-stack-gaps.json';

const STACK_GAP_TOKENS = ['none', 'xs', 'sm', 'md', 'default', 'relaxed', 'loose'] as const;

export type StackGap = (typeof STACK_GAP_TOKENS)[number];

export type StackGapClassMap = Readonly<Record<StackGap, string>>;

function isStackGap(key: string): key is StackGap {
    for (const token of STACK_GAP_TOKENS) {
        if (token === key) {
            return true;
        }
    }

    return false;
}

function isCompleteStackGapMap(map: Partial<Record<StackGap, string>>): map is StackGapClassMap {
    return STACK_GAP_TOKENS.every((k) => typeof map[k] === 'string' && map[k].length > 0);
}

function requireJsonObject(value: unknown): object {
    if (typeof value !== 'object' || value === null) {
        throw new Error('ui-stack-gaps.json: invalid root');
    }

    return value;
}

function requireStackGapObject(root: object): object {
    const stackGap = Reflect.get(root, 'stackGap');
    if (typeof stackGap !== 'object' || stackGap === null) {
        throw new Error('ui-stack-gaps.json: missing stackGap object');
    }

    return stackGap;
}

export function parseStackGapMapFromUnknown(value: unknown): StackGapClassMap {
    const root = requireJsonObject(value);
    const stackGap = requireStackGapObject(root);
    const out: Partial<Record<StackGap, string>> = {};
    for (const [key, gapClass] of Object.entries(stackGap)) {
        if (isStackGap(key) && typeof gapClass === 'string') {
            out[key] = gapClass;
        }
    }

    if (!isCompleteStackGapMap(out)) {
        throw new Error('ui-stack-gaps.json: incomplete or invalid stackGap map');
    }

    return out;
}

export const STACK_GAP_CLASS_BY_TOKEN: StackGapClassMap = parseStackGapMapFromUnknown(raw);
