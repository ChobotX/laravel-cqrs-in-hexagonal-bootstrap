/** Narrow `Event#target` for `closest()` / DOM traversal. */
export function htmlElementFromEventTarget(target: Event['target']): HTMLElement | null {
    return target instanceof HTMLElement ? target : null;
}

/** Narrow `Event#target` for `contains()` / `Node` APIs. */
export function nodeFromEventTarget(target: Event['target']): Node | null {
    return target instanceof Node ? target : null;
}

/** `document.activeElement` narrowed to `HTMLElement` (or null). */
export function activeHtmlElement(): HTMLElement | null {
    const el = document.activeElement;
    return el instanceof HTMLElement ? el : null;
}
