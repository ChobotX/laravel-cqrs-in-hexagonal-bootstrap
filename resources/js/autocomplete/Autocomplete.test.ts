import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeAll, beforeEach, describe, expect, it, vi } from 'vitest';
import Autocomplete from './Autocomplete.vue';

beforeAll(() => {
    Element.prototype.scrollIntoView = (): void => undefined;
});

interface SearchResult {
    id: string;
    name: string;
    email: string;
}

interface SearchResponse {
    data: SearchResult[];
}

const mockResults: SearchResponse = {
    data: [
        { id: 'user-1', name: 'Alice Johnson', email: 'alice@example.com' },
        { id: 'user-2', name: 'Bob Smith', email: 'bob@example.com' },
    ],
};

const emptyResults: SearchResponse = { data: [] };

function createFetchMock(response: unknown = mockResults): void {
    global.fetch = vi.fn().mockResolvedValue({
        json: () => Promise.resolve(response),
    });
}

function mountAutocomplete(props: Partial<InstanceType<typeof Autocomplete>['$props']> = {}): ReturnType<typeof mount> {
    return mount(Autocomplete, {
        props: {
            searchUrl: '/internal-api/users/search',
            excludeIds: [],
            inputName: 'user_id',
            placeholder: 'Search users...',
            debounceMs: 0,
            ...props,
        },
    });
}

async function typeAndFetch(wrapper: ReturnType<typeof mount>, value: string): Promise<void> {
    const input = wrapper.find('input[role="combobox"]');
    await input.setValue(value);
    await input.trigger('input');
    await vi.advanceTimersByTimeAsync(0);
    await flushPromises();
}

describe('Autocomplete', () => {
    beforeEach(() => {
        vi.useFakeTimers();
        createFetchMock();
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.restoreAllMocks();
    });

    it('renders combobox input', () => {
        const wrapper = mountAutocomplete();

        expect(wrapper.find('input[role="combobox"]').exists()).toBe(true);
        expect(wrapper.find('input[role="combobox"]').attributes('placeholder')).toBe('Search users...');
    });

    it('does not fetch when less than minChars typed', async () => {
        const wrapper = mountAutocomplete({ minChars: 2 });

        await typeAndFetch(wrapper, 'a');

        expect(global.fetch).not.toHaveBeenCalled();
    });

    it('fetches results on focus for initial load', async () => {
        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledTimes(1);
        const options = wrapper.findAll('[role="option"]');
        expect(options).toHaveLength(2);
    });

    it('does not fetch on focus when item is already selected', async () => {
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');
        await wrapper.findAll('[role="option"]')[0].trigger('mousedown');

        (global.fetch as ReturnType<typeof vi.fn>).mockClear();

        const input = wrapper.find('input[role="combobox"]');
        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).not.toHaveBeenCalled();
    });

    it('does not re-fetch on focus when results already loaded', async () => {
        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledTimes(1);

        (global.fetch as ReturnType<typeof vi.fn>).mockClear();

        await input.trigger('blur');
        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).not.toHaveBeenCalled();
    });

    it('fetches and displays results after debounce', async () => {
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'Ali');

        const options = wrapper.findAll('[role="option"]');
        expect(options).toHaveLength(2);
        expect(options[0].text()).toContain('Alice Johnson');
        expect(options[0].text()).toContain('alice@example.com');
    });

    it('passes exclude ids as query params', async () => {
        const wrapper = mountAutocomplete({ excludeIds: ['user-x', 'user-y'] });

        await typeAndFetch(wrapper, 'test');

        const fetchUrl = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][0] as string;
        expect(fetchUrl).toContain('exclude%5B%5D=user-x');
        expect(fetchUrl).toContain('exclude%5B%5D=user-y');
    });

    it('keyboard navigation: ArrowDown, ArrowUp, Enter select', async () => {
        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await typeAndFetch(wrapper, 'test');

        await input.trigger('keydown', { key: 'ArrowDown' });
        let activeOptions = wrapper.findAll('[data-active="true"]');
        expect(activeOptions).toHaveLength(1);
        expect(activeOptions[0].text()).toContain('Alice Johnson');

        await input.trigger('keydown', { key: 'ArrowDown' });
        activeOptions = wrapper.findAll('[data-active="true"]');
        expect(activeOptions[0].text()).toContain('Bob Smith');

        await input.trigger('keydown', { key: 'ArrowUp' });
        activeOptions = wrapper.findAll('[data-active="true"]');
        expect(activeOptions[0].text()).toContain('Alice Johnson');

        await input.trigger('keydown', { key: 'Enter' });
        expect(wrapper.find('input[type="hidden"][value="user-1"]').exists()).toBe(true);
    });

    it('escape closes dropdown', async () => {
        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await typeAndFetch(wrapper, 'test');

        expect(wrapper.findAll('[role="option"]').length).toBeGreaterThan(0);

        await input.trigger('keydown', { key: 'Escape' });
        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
    });

    it('selecting item populates hidden input and shows display value', async () => {
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');

        const options = wrapper.findAll('[role="option"]');
        await options[0].trigger('mousedown');

        expect(wrapper.find('input[type="hidden"][value="user-1"]').exists()).toBe(true);
        const inputEl = wrapper.find<HTMLInputElement>('input[role="combobox"]');
        expect(inputEl.element.value).toContain('Alice Johnson');
    });

    it('clearing resets state', async () => {
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');

        await wrapper.findAll('[role="option"]')[0].trigger('mousedown');
        expect(wrapper.find('input[type="hidden"]').exists()).toBe(true);

        const clearButton = wrapper.find('button[aria-label="Clear selection"]');
        await clearButton.trigger('mousedown');

        expect(wrapper.find('input[type="hidden"]').exists()).toBe(false);
        const inputEl = wrapper.find<HTMLInputElement>('input[role="combobox"]');
        expect(inputEl.element.value).toBe('');
    });

    it('shows no results message when fetch returns empty', async () => {
        createFetchMock(emptyResults);
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'nonexistent');

        expect(wrapper.text()).toContain('No results found.');
        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
    });

    it('shows loading state during fetch', async () => {
        let resolvePromise: ((value: unknown) => void) | undefined;
        global.fetch = vi.fn().mockReturnValue(
            new Promise((resolve) => {
                resolvePromise = resolve;
            }),
        );

        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await input.setValue('test');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);

        expect(wrapper.find('[aria-live="polite"]').exists()).toBe(true);

        resolvePromise?.({ json: () => Promise.resolve(mockResults) });
        await flushPromises();

        expect(wrapper.find('[aria-live="polite"]').exists()).toBe(false);
    });

    it('handles fetch error gracefully', async () => {
        global.fetch = vi.fn().mockRejectedValue(new Error('Network error'));

        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');

        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
    });

    it('sends csrf token when meta tag exists', async () => {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = 'test-csrf-token';
        document.head.appendChild(meta);

        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');

        const fetchHeaders = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][1].headers;
        expect(fetchHeaders['X-CSRF-TOKEN']).toBe('test-csrf-token');

        document.head.removeChild(meta);
    });

    it('typing after selecting clears selection', async () => {
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');

        await wrapper.findAll('[role="option"]')[0].trigger('mousedown');
        expect(wrapper.find('input[type="hidden"]').exists()).toBe(true);

        const input = wrapper.find('input[role="combobox"]');
        await input.setValue('new');
        await input.trigger('input');

        expect(wrapper.find('input[type="hidden"]').exists()).toBe(false);
    });

    it('handles response without data property', async () => {
        createFetchMock({});
        const wrapper = mountAutocomplete();

        await typeAndFetch(wrapper, 'test');

        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
    });

    it('enter with no active index does not select', async () => {
        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await typeAndFetch(wrapper, 'test');
        expect(wrapper.findAll('[role="option"]').length).toBeGreaterThan(0);

        await input.trigger('keydown', { key: 'Enter' });

        expect(wrapper.find('input[type="hidden"]').exists()).toBe(false);
    });

    it('arrow keys are ignored when dropdown is closed', async () => {
        const wrapper = mountAutocomplete();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('keydown', { key: 'ArrowDown' });
        expect(wrapper.findAll('[data-active="true"]')).toHaveLength(0);
    });
});
