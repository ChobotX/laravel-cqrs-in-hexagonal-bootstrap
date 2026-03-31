import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeAll, beforeEach, describe, expect, it, vi } from 'vitest';
import LazyChipSelector from './LazyChipSelector.vue';

vi.mock('laravel-vue-i18n', () => ({
    trans: (key: string): string => key,
}));

vi.mock('../toast/toast-queue', () => ({
    error: vi.fn(),
}));

beforeAll(() => {
    Element.prototype.scrollIntoView = (): void => undefined;
});

const i18nMock = { $t: (key: string): string => key };

interface SearchResponse {
    data: Array<{ id: string; name: string; email?: string }>;
}

const mockResults: SearchResponse = {
    data: [
        { id: 'org-1', name: 'Acme Corp', email: 'Acme description' },
        { id: 'org-2', name: 'Beta Inc', email: 'Beta description' },
    ],
};

const emptyResults: SearchResponse = { data: [] };

function createFetchMock(response: unknown = mockResults): void {
    global.fetch = vi.fn().mockResolvedValue({
        ok: true,
        json: () => Promise.resolve(response),
    });
}

function mountLazy(props: Partial<InstanceType<typeof LazyChipSelector>['$props']> = {}): ReturnType<typeof mount> {
    return mount(LazyChipSelector, {
        props: {
            searchUrl: '/internal-api/teams/search',
            selectedItems: [],
            debounceMs: 0,
            ...props,
        },
        global: { mocks: i18nMock },
    });
}

describe('LazyChipSelector', () => {
    beforeEach(() => {
        vi.useFakeTimers();
        createFetchMock();
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.restoreAllMocks();
    });

    it('renders chips for pre-selected items', () => {
        const wrapper = mountLazy({
            selectedItems: [{ id: 'org-x', name: 'Pre-selected Org' }],
        });

        expect(wrapper.text()).toContain('Pre-selected Org');
        expect(wrapper.find('input[type="hidden"][value="org-x"]').exists()).toBe(true);
    });

    it('does not fetch on mount', () => {
        mountLazy();

        expect(global.fetch).not.toHaveBeenCalled();
    });

    it('fetches initial results on focus', async () => {
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledTimes(1);
        const fetchUrl = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][0] as string;
        expect(fetchUrl).toContain('/internal-api/teams/search');
    });

    it('shows loading indicator during fetch', async () => {
        let resolvePromise: ((value: unknown) => void) | undefined;
        global.fetch = vi.fn().mockReturnValue(
            new Promise((resolve) => {
                resolvePromise = resolve;
            }),
        );

        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        expect(wrapper.find('[aria-live="polite"]').exists()).toBe(true);

        resolvePromise?.({ ok: true, json: () => Promise.resolve(mockResults) });
        await flushPromises();

        expect(wrapper.find('[aria-live="polite"]').exists()).toBe(false);
    });

    it('shows fetched options in dropdown after fetch', async () => {
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        const options = wrapper.findAll('[role="option"]');
        expect(options).toHaveLength(2);
        expect(options[0].text()).toContain('Acme Corp');
        expect(options[1].text()).toContain('Beta Inc');
    });

    it('typing triggers debounced search', async () => {
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        (global.fetch as ReturnType<typeof vi.fn>).mockClear();

        await input.setValue('Acme');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledTimes(1);
        const fetchUrl = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][0] as string;
        expect(fetchUrl).toContain('q=Acme');
    });

    it('selecting an option adds chip and re-fetches with updated exclude', async () => {
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        (global.fetch as ReturnType<typeof vi.fn>).mockClear();
        createFetchMock({ data: [{ id: 'org-2', name: 'Beta Inc', email: 'Beta description' }] });

        const options = wrapper.findAll('[role="option"]');
        await options[0].trigger('mousedown');
        await flushPromises();

        expect(wrapper.find('input[type="hidden"][value="org-1"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('Acme Corp');

        expect(global.fetch).toHaveBeenCalledTimes(1);
        const fetchUrl = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][0] as string;
        expect(fetchUrl).toContain('exclude%5B%5D=org-1');
    });

    it('removing a chip removes hidden input', async () => {
        const wrapper = mountLazy({
            selectedItems: [{ id: 'org-x', name: 'Remove Me' }],
        });

        expect(wrapper.find('input[type="hidden"][value="org-x"]').exists()).toBe(true);

        const removeButton = wrapper.find('button[aria-label="Remove Remove Me"]');
        await removeButton.trigger('click');
        await flushPromises();

        expect(wrapper.find('input[type="hidden"][value="org-x"]').exists()).toBe(false);
    });

    it('shows empty state message when fetch returns 0 results', async () => {
        createFetchMock(emptyResults);
        const wrapper = mountLazy({ noResultsText: 'No teams found' });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        await input.setValue('zzzzz');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        expect(wrapper.text()).toContain('No teams found');
    });

    it('passes exclude[] for all selected IDs in fetch request', async () => {
        const wrapper = mountLazy({
            selectedItems: [
                { id: 'org-a', name: 'Org A' },
                { id: 'org-b', name: 'Org B' },
            ],
        });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        const fetchUrl = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][0] as string;
        expect(fetchUrl).toContain('exclude%5B%5D=org-a');
        expect(fetchUrl).toContain('exclude%5B%5D=org-b');
    });

    it('uses custom inputName for hidden inputs', () => {
        const wrapper = mountLazy({
            selectedItems: [{ id: 'org-x', name: 'Org X' }],
            inputName: 'teams[]',
        });

        const hidden = wrapper.find('input[type="hidden"][value="org-x"]');
        expect(hidden.attributes('name')).toBe('teams[]');
    });

    it('does not re-fetch on subsequent focus if already fetched', async () => {
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledTimes(1);

        await input.trigger('blur');
        await input.trigger('focus');
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledTimes(1);
    });

    it('maps email field to badge in options', async () => {
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(wrapper.text()).toContain('(Acme description)');
    });

    it('sends csrf token when meta tag exists', async () => {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = 'test-csrf-token';
        document.head.appendChild(meta);

        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        const fetchHeaders = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][1].headers;
        expect(fetchHeaders['X-CSRF-TOKEN']).toBe('test-csrf-token');

        document.head.removeChild(meta);
    });

    it('handles response without data property', async () => {
        createFetchMock({});
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
    });

    it('maps description field to badge when email is absent', async () => {
        createFetchMock({ data: [{ id: 'role-1', name: 'Editor', description: 'Edits content' }] });
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(wrapper.text()).toContain('(Edits content)');
    });

    it('omits badge when email is empty string', async () => {
        createFetchMock({ data: [{ id: 'org-1', name: 'No Email Org', email: '' }] });
        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        const options = wrapper.findAll('[role="option"]');
        expect(options).toHaveLength(1);
        expect(options[0].text()).toBe('No Email Org');
        expect(options[0].text()).not.toContain('(');
    });

    it('shows error toast on fetch failure', async () => {
        const { error: errorToast } = await import('../toast/toast-queue');
        global.fetch = vi.fn().mockRejectedValue(new Error('Network error'));

        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
        expect(wrapper.find('[aria-live="polite"]').exists()).toBe(false);
        expect(errorToast).toHaveBeenCalledWith('messages.labels.search_failed');
    });

    it('shows error toast on non-ok search response', async () => {
        const { error: errorToast } = await import('../toast/toast-queue');
        global.fetch = vi.fn().mockResolvedValue({ ok: false, status: 403 });

        const wrapper = mountLazy();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
        expect(errorToast).toHaveBeenCalledWith('messages.labels.search_failed');
    });

    it('calls createUrl when create event is received', async () => {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = 'create-csrf-token';
        document.head.appendChild(meta);

        createFetchMock(emptyResults);

        const createResponse = { data: { id: 'new-1', name: 'newlabel' } };
        const fetchMock = vi
            .fn()
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(createResponse) })
            .mockResolvedValue({ ok: true, json: () => Promise.resolve(emptyResults) });
        global.fetch = fetchMock;

        const wrapper = mountLazy({
            allowCreate: true,
            createUrl: '/internal-api/labels',
            createNamespace: 'users',
        });

        const input = wrapper.find('input[role="combobox"]');
        await input.trigger('focus');
        await flushPromises();

        await input.setValue('newlabel');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        const createOption = wrapper.findAll('[role="option"]').find((o) => o.text().includes('Create'));
        expect(createOption).toBeDefined();
        await createOption?.trigger('mousedown');
        await flushPromises();

        const createCall = fetchMock.mock.calls.find((call) => call[1]?.method === 'POST');
        expect(createCall).toBeDefined();
        expect(createCall![0]).toBe('/internal-api/labels');
        const body = JSON.parse(createCall![1].body as string);
        expect(body).toEqual({ namespace: 'users', name: 'newlabel' });

        const createHeaders = createCall![1].headers;
        expect(createHeaders['X-CSRF-TOKEN']).toBe('create-csrf-token');

        document.head.removeChild(meta);
    });

    it('adds created label to selection after create success', async () => {
        const createResponse = { data: { id: 'new-1', name: 'newlabel' } };
        const fetchMock = vi
            .fn()
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(createResponse) })
            .mockResolvedValue({ ok: true, json: () => Promise.resolve(emptyResults) });
        global.fetch = fetchMock;

        const wrapper = mountLazy({
            allowCreate: true,
            createUrl: '/internal-api/labels',
            createNamespace: 'users',
        });

        const input = wrapper.find('input[role="combobox"]');
        await input.trigger('focus');
        await flushPromises();

        await input.setValue('newlabel');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        const createOption = wrapper.findAll('[role="option"]').find((o) => o.text().includes('Create'));
        await createOption?.trigger('mousedown');
        await flushPromises();

        expect(wrapper.find('input[type="hidden"][value="new-1"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('newlabel');
    });

    it('shows error toast on create failure', async () => {
        const { error: errorToast } = await import('../toast/toast-queue');
        const fetchMock = vi
            .fn()
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockRejectedValueOnce(new Error('Create failed'));
        global.fetch = fetchMock;

        const wrapper = mountLazy({
            allowCreate: true,
            createUrl: '/internal-api/labels',
            createNamespace: 'users',
        });

        const input = wrapper.find('input[role="combobox"]');
        await input.trigger('focus');
        await flushPromises();

        await input.setValue('fail');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        const createOption = wrapper.findAll('[role="option"]').find((o) => o.text().includes('Create'));
        await createOption?.trigger('mousedown');
        await flushPromises();

        expect(wrapper.find('input[type="hidden"][value="new-1"]').exists()).toBe(false);
        expect(errorToast).toHaveBeenCalledWith('messages.labels.create_failed');
    });

    it('shows error toast on non-ok create response', async () => {
        const { error: errorToast } = await import('../toast/toast-queue');
        const fetchMock = vi
            .fn()
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockResolvedValueOnce({ ok: true, json: () => Promise.resolve(emptyResults) })
            .mockResolvedValueOnce({ ok: false, status: 422 });
        global.fetch = fetchMock;

        const wrapper = mountLazy({
            allowCreate: true,
            createUrl: '/internal-api/labels',
            createNamespace: 'users',
        });

        const input = wrapper.find('input[role="combobox"]');
        await input.trigger('focus');
        await flushPromises();

        await input.setValue('bad');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        const createOption = wrapper.findAll('[role="option"]').find((o) => o.text().includes('Create'));
        await createOption?.trigger('mousedown');
        await flushPromises();

        expect(wrapper.find('input[type="hidden"][value="new-1"]').exists()).toBe(false);
        expect(errorToast).toHaveBeenCalledWith('messages.labels.create_failed');
    });

    it('appends params with & when searchUrl already contains query string', async () => {
        const wrapper = mountLazy({
            searchUrl: '/internal-api/labels/search?namespace=users',
            selectedItems: [{ id: 'lbl-1', name: 'Label A' }],
        });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await flushPromises();

        const fetchUrl = (global.fetch as ReturnType<typeof vi.fn>).mock.calls[0][0] as string;
        expect(fetchUrl).toContain('/internal-api/labels/search?namespace=users&');
        expect(fetchUrl).not.toContain('?exclude');
    });

    it('does not call createUrl when createUrl is not set', async () => {
        createFetchMock(emptyResults);

        const wrapper = mountLazy({ allowCreate: true });

        const input = wrapper.find('input[role="combobox"]');
        await input.trigger('focus');
        await flushPromises();

        await input.setValue('test');
        await input.trigger('input');
        await vi.advanceTimersByTimeAsync(0);
        await flushPromises();

        const callCount = (global.fetch as ReturnType<typeof vi.fn>).mock.calls.length;

        const createOption = wrapper.findAll('[role="option"]').find((o) => o.text().includes('Create'));
        if (createOption) {
            await createOption.trigger('mousedown');
            await flushPromises();
        }

        expect((global.fetch as ReturnType<typeof vi.fn>).mock.calls.length).toBe(callCount);
    });
});
