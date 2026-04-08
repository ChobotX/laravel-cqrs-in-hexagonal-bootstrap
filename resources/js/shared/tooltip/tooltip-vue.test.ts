import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';
import Tooltip from './Tooltip.vue';

function mountTooltip(props: Record<string, unknown> = {}): ReturnType<typeof mount> {
    return mount(Tooltip, {
        props,
        slots: {
            activator: '<button>Trigger</button>',
            default: 'Tooltip content',
        },
        attachTo: document.body,
    });
}

function getTooltipEl(): HTMLElement {
    return document.querySelector('[role="tooltip"]') as HTMLElement;
}

afterEach(() => {
    vi.restoreAllMocks();
    document.body.innerHTML = '';
});

describe('Tooltip', () => {
    it('renders activator slot', () => {
        const wrapper = mountTooltip();
        expect(wrapper.find('button').text()).toBe('Trigger');
    });

    it('tooltip is hidden by default', () => {
        mountTooltip();
        expect(getTooltipEl().classList.contains('tooltip--visible')).toBe(false);
    });

    it('shows tooltip on mouseenter and hides on mouseleave', async () => {
        const wrapper = mountTooltip();

        wrapper.element.dispatchEvent(new MouseEvent('mouseenter'));
        await nextTick();

        expect(getTooltipEl().classList.contains('tooltip--visible')).toBe(true);

        wrapper.element.dispatchEvent(new MouseEvent('mouseleave'));
        await nextTick();

        expect(getTooltipEl().classList.contains('tooltip--visible')).toBe(false);
    });

    it('shows tooltip on focusin and hides on focusout', async () => {
        const wrapper = mountTooltip();

        wrapper.element.dispatchEvent(new FocusEvent('focusin'));
        await nextTick();

        expect(getTooltipEl().classList.contains('tooltip--visible')).toBe(true);

        wrapper.element.dispatchEvent(new FocusEvent('focusout'));
        await nextTick();

        expect(getTooltipEl().classList.contains('tooltip--visible')).toBe(false);
    });

    it('renders tooltip content from default slot', () => {
        mountTooltip();
        expect(getTooltipEl().textContent).toContain('Tooltip content');
    });

    it('uses top position class by default', () => {
        mountTooltip();
        expect(getTooltipEl().classList.contains('tooltip--top')).toBe(true);
    });

    it('uses position class from prop', () => {
        mountTooltip({ position: 'bottom' });
        expect(getTooltipEl().classList.contains('tooltip--bottom')).toBe(true);
    });

    it('has role=tooltip on popup', () => {
        mountTooltip();
        expect(getTooltipEl()).not.toBeNull();
        expect(getTooltipEl().getAttribute('role')).toBe('tooltip');
    });

    it('teleports tooltip to body', () => {
        mountTooltip();
        expect(document.body.querySelector(':scope > [role="tooltip"]')).not.toBeNull();
    });

    it('wraps activator in inline-flex container', () => {
        const wrapper = mountTooltip();
        expect(wrapper.element.classList.contains('inline-flex')).toBe(true);
    });
});
