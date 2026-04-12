import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

const AUTO_SUBMIT_HTML = `
<form id="test-form">
    <select id="auto-select" data-auto-submit>
        <option value="a">A</option>
        <option value="b">B</option>
    </select>
    <select id="normal-select">
        <option value="x">X</option>
        <option value="y">Y</option>
    </select>
</form>
`;

beforeEach(async () => {
    document.body.innerHTML = AUTO_SUBMIT_HTML;
    await import('./auto-submit');
});

afterEach(() => {
    document.body.innerHTML = '';
});

function triggerChange(el: Element): void {
    el.dispatchEvent(new Event('change', { bubbles: true }));
}

describe('auto-submit', () => {
    it('submits form on change of element with data-auto-submit', () => {
        const formEl = document.getElementById('test-form');
        const selectEl = document.getElementById('auto-select');
        expect(formEl).toBeInstanceOf(HTMLFormElement);
        expect(selectEl).toBeInstanceOf(HTMLSelectElement);
        const form = formEl;
        const select = selectEl;
        const submitSpy = vi.spyOn(form, 'submit').mockImplementation(vi.fn());

        triggerChange(select);

        expect(submitSpy).toHaveBeenCalledOnce();
    });

    it('does not submit form on change of normal element', () => {
        const formEl = document.getElementById('test-form');
        const selectEl = document.getElementById('normal-select');
        expect(formEl).toBeInstanceOf(HTMLFormElement);
        expect(selectEl).toBeInstanceOf(HTMLSelectElement);
        const form = formEl;
        const select = selectEl;
        const submitSpy = vi.spyOn(form, 'submit').mockImplementation(vi.fn());

        triggerChange(select);

        expect(submitSpy).not.toHaveBeenCalled();
    });

    it('handles element without parent form gracefully', () => {
        const orphan = document.createElement('select');
        orphan.setAttribute('data-auto-submit', '');
        document.body.appendChild(orphan);

        triggerChange(orphan);
    });

    it('ignores change events whose target is not an HTMLElement', () => {
        const formEl = document.getElementById('test-form');
        expect(formEl).toBeInstanceOf(HTMLFormElement);
        const submitSpy = vi.spyOn(formEl, 'submit').mockImplementation(vi.fn());

        const ev = new Event('change', { bubbles: true });
        Object.defineProperty(ev, 'target', { value: document.createTextNode(''), enumerable: true });
        document.dispatchEvent(ev);

        expect(submitSpy).not.toHaveBeenCalled();
    });
});
