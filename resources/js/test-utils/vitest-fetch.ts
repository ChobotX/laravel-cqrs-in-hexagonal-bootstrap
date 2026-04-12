import { isRecord } from '../shared/type-guards/is-record';

/** Vitest mock or `vi.mocked(global.fetch)` — anything with `.mock.calls`. */
type CallMock = { mock: { calls: unknown[][] } };

export function mockedFetchFirstUrl(mockFetch: CallMock): string {
    const first = mockFetch.mock.calls[0]?.[0];
    if (typeof first !== 'string') {
        throw new Error('Expected fetch mock first argument to be a string URL');
    }
    return first;
}

export function mockedFetchFirstInitBodyString(mockFetch: CallMock): string {
    const init = mockFetch.mock.calls[0]?.[1];
    if (!isRecord(init) || typeof init.body !== 'string') {
        throw new Error('Expected fetch mock RequestInit with string body');
    }
    return init.body;
}

export function mockedFetchFirstInitRecord(mockFetch: CallMock): Record<string, unknown> {
    const init = mockFetch.mock.calls[0]?.[1];
    if (!isRecord(init)) {
        throw new Error('Expected fetch mock RequestInit object');
    }
    return init;
}
