import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import ToastItem from './ToastItem.vue';
import { ToastType } from './toast-queue';

const TOAST_ID_CLOSE = 3;

vi.mock('./toast-queue', async () => {
    const actual = await vi.importActual<typeof import('./toast-queue')>('./toast-queue');
    return { ...actual, removeToast: vi.fn() };
});

describe('ToastItem', () => {
    it('renders success variant with correct styles', () => {
        const wrapper = mount(ToastItem, {
            props: {
                toast: { id: 1, type: ToastType.Success, message: 'Created!', removing: false },
            },
        });

        expect(wrapper.text()).toContain('Created!');
        expect(wrapper.find('[role="alert"]').classes()).toContain('border-green-500');
        expect(wrapper.find('[role="alert"]').classes()).toContain('opacity-100');
    });

    it('renders error variant with correct styles', () => {
        const wrapper = mount(ToastItem, {
            props: {
                toast: { id: 2, type: ToastType.Error, message: 'Failed!', removing: false },
            },
        });

        expect(wrapper.text()).toContain('Failed!');
        expect(wrapper.find('[role="alert"]').classes()).toContain('border-red-500');
    });

    it('applies removing styles when toast is being removed', () => {
        const wrapper = mount(ToastItem, {
            props: {
                toast: { id: 1, type: ToastType.Success, message: 'Bye', removing: true },
            },
        });

        expect(wrapper.find('[role="alert"]').classes()).toContain('opacity-0');
    });

    it('close button calls removeToast', async () => {
        const { removeToast } = await import('./toast-queue');

        const wrapper = mount(ToastItem, {
            props: {
                toast: { id: TOAST_ID_CLOSE, type: ToastType.Success, message: 'Test', removing: false },
            },
        });

        await wrapper.find('button').trigger('click');
        expect(removeToast).toHaveBeenCalledWith(TOAST_ID_CLOSE);
    });
});
