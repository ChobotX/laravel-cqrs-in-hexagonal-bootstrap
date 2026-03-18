import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import ToastContainer from './ToastContainer.vue';
import { clearToasts, resetNextId, ToastType, toasts } from './toast-queue';

afterEach(() => {
    clearToasts();
    resetNextId();
});

describe('ToastContainer', () => {
    it('renders no toasts when queue is empty', () => {
        const wrapper = mount(ToastContainer);
        expect(wrapper.findAll('[role="alert"]')).toHaveLength(0);
    });

    it('renders toasts from the queue', async () => {
        toasts.push({ id: 1, type: ToastType.Success, message: 'Created!', removing: false });
        toasts.push({ id: 2, type: ToastType.Error, message: 'Failed!', removing: false });

        const wrapper = mount(ToastContainer);
        await wrapper.vm.$nextTick();

        expect(wrapper.findAll('[role="alert"]')).toHaveLength(2);
        expect(wrapper.text()).toContain('Created!');
        expect(wrapper.text()).toContain('Failed!');
    });

    it('has correct ARIA attributes for live region', () => {
        const wrapper = mount(ToastContainer);

        const container = wrapper.find('[aria-live="polite"]');
        expect(container.exists()).toBe(true);
        expect(container.attributes('role')).toBe('status');
    });
});
