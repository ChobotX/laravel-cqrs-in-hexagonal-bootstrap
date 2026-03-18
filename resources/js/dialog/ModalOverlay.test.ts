import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import ModalOverlay from './ModalOverlay.vue';

afterEach(() => {
    document.body.innerHTML = '';
    document.body.style.overflow = '';
});

describe('ModalOverlay', () => {
    it('renders slot content via teleport', () => {
        mount(ModalOverlay, {
            slots: { default: '<div data-testid="slot-content">Hello</div>' },
        });

        const slotContent: Element | null = document.querySelector('[data-testid="slot-content"]');
        expect(slotContent).not.toBeNull();
        expect(slotContent?.textContent).toBe('Hello');
    });

    it('emits close on Escape key', () => {
        const wrapper = mount(ModalOverlay, {
            slots: { default: '<div>Content</div>' },
        });

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));

        expect(wrapper.emitted('close')).toHaveLength(1);
    });

    it('has correct ARIA attributes', () => {
        mount(ModalOverlay, {
            slots: { default: '<div>Content</div>' },
        });

        const dialog: Element | null = document.querySelector('[role="dialog"]');
        expect(dialog).not.toBeNull();
        expect(dialog?.getAttribute('aria-modal')).toBe('true');
        expect(dialog?.getAttribute('aria-labelledby')).toBe('dialog-title');
    });

    it('prevents body scroll while open', () => {
        mount(ModalOverlay, {
            slots: { default: '<div>Content</div>' },
        });

        expect(document.body.style.overflow).toBe('hidden');
    });

    it('restores body scroll on unmount', () => {
        const wrapper = mount(ModalOverlay, {
            slots: { default: '<div>Content</div>' },
        });

        expect(document.body.style.overflow).toBe('hidden');
        wrapper.unmount();
        expect(document.body.style.overflow).toBe('');
    });

    it('emits close on backdrop click', () => {
        const wrapper = mount(ModalOverlay, {
            slots: { default: '<div>Content</div>' },
        });

        const backdrop: Element | null = document.querySelector('.bg-gray-900\\/80');
        expect(backdrop).not.toBeNull();
        backdrop?.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(wrapper.emitted('close')).toHaveLength(1);
    });

    it('traps focus: Tab on last element wraps to first', () => {
        mount(ModalOverlay, {
            slots: {
                default: '<div><button data-testid="btn1">First</button><button data-testid="btn2">Last</button></div>',
            },
        });

        const btn1: HTMLElement | null = document.querySelector('[data-testid="btn1"]');
        const btn2: HTMLElement | null = document.querySelector('[data-testid="btn2"]');
        expect(btn1).not.toBeNull();
        expect(btn2).not.toBeNull();

        btn2?.focus();
        const event = new KeyboardEvent('keydown', { key: 'Tab', bubbles: true, cancelable: true });
        document.dispatchEvent(event);
    });

    it('traps focus: Shift+Tab on first element wraps to last', () => {
        mount(ModalOverlay, {
            slots: {
                default: '<div><button data-testid="btn1">First</button><button data-testid="btn2">Last</button></div>',
            },
        });

        const btn1: HTMLElement | null = document.querySelector('[data-testid="btn1"]');
        expect(btn1).not.toBeNull();

        btn1?.focus();
        const event = new KeyboardEvent('keydown', { key: 'Tab', shiftKey: true, bubbles: true, cancelable: true });
        document.dispatchEvent(event);
    });

    it('does not trap on non-Tab keys', () => {
        const wrapper = mount(ModalOverlay, {
            slots: { default: '<div><button>Btn</button></div>' },
        });

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'a' }));
        expect(wrapper.emitted('close')).toBeUndefined();
    });

    it('prevents Tab when no focusable elements exist', () => {
        mount(ModalOverlay, {
            slots: { default: '<div><span>No buttons</span></div>' },
        });

        const event = new KeyboardEvent('keydown', { key: 'Tab', bubbles: true, cancelable: true });
        document.dispatchEvent(event);
    });

    it('restores focus to previously active element on unmount', () => {
        const externalButton: HTMLButtonElement = document.createElement('button');
        externalButton.textContent = 'External';
        document.body.appendChild(externalButton);
        externalButton.focus();

        const wrapper = mount(ModalOverlay, {
            slots: { default: '<div><button>Inside</button></div>' },
        });

        wrapper.unmount();
        expect(document.activeElement).toBe(externalButton);
    });

    it('does not restore focus when previous element is not HTMLElement', () => {
        const svg: SVGSVGElement = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('tabindex', '0');
        document.body.appendChild(svg);
        svg.focus();
        expect(document.activeElement).toBe(svg);

        const wrapper = mount(ModalOverlay, {
            slots: { default: '<div><button>Inside</button></div>' },
        });

        wrapper.unmount();
        expect(document.body.style.overflow).toBe('');
    });
});
