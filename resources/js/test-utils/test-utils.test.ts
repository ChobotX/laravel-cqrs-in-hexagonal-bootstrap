import { afterEach, describe, expect, it } from 'vitest';
import {
    queryHTMLElementById,
    queryHTMLTimeElementById,
    requireChildElement,
    requireDocumentButton,
    requireDocumentElement,
    requireNthDocumentButton,
    requireSvgPathElement,
} from './dom';
import { mockedFetchFirstInitBodyString, mockedFetchFirstInitRecord, mockedFetchFirstUrl } from './vitest-fetch';

describe('dom helpers', () => {
    afterEach(() => {
        document.body.innerHTML = '';
    });

    it('queryHTMLElementById returns null for missing id', () => {
        expect(queryHTMLElementById('missing')).toBeNull();
    });

    it('queryHTMLElementById returns div as HTMLElement', () => {
        const div = document.createElement('div');
        div.id = 'd2';
        document.body.append(div);
        expect(queryHTMLElementById('d2')).toBe(div);
    });

    it('queryHTMLTimeElementById narrows time elements', () => {
        const time = document.createElement('time');
        time.id = 't1';
        document.body.append(time);
        expect(queryHTMLTimeElementById('t1')).toBe(time);

        const div = document.createElement('div');
        div.id = 'd1';
        document.body.append(div);
        expect(queryHTMLTimeElementById('d1')).toBeNull();
    });

    it('requireDocumentButton and requireNthDocumentButton assert button elements', () => {
        document.body.innerHTML = '<button type="button">A</button><button type="button">B</button>';
        expect(requireDocumentButton().textContent).toBe('A');
        expect(requireNthDocumentButton(1).textContent).toBe('B');
    });

    it('requireDocumentElement and requireChildElement assert HTMLElement', () => {
        document.body.innerHTML = '<div id="root"><span class="inner">x</span></div>';
        const root = document.getElementById('root');
        expect(root).not.toBeNull();
        expect(requireDocumentElement('#root')).toBe(root);
        if (!(root instanceof HTMLElement)) {
            throw new Error('expected root HTMLElement');
        }
        expect(requireChildElement(root, '.inner').textContent).toBe('x');
    });

    it('requireSvgPathElement asserts path element', () => {
        document.body.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0"/></svg>';
        expect(requireSvgPathElement().tagName.toLowerCase()).toBe('path');
    });
});

describe('vitest-fetch helpers', () => {
    it('mockedFetchFirstUrl throws when first argument is not a string', () => {
        const mockFetch = { mock: { calls: [[1, {}]] } };
        expect(() => mockedFetchFirstUrl(mockFetch)).toThrow('Expected fetch mock first argument to be a string URL');
    });

    it('mockedFetchFirstUrl throws when call has no URL argument', () => {
        const mockFetch = { mock: { calls: [[]] } };
        expect(() => mockedFetchFirstUrl(mockFetch)).toThrow('Expected fetch mock first argument to be a string URL');
    });

    it('mockedFetchFirstInitBodyString throws when init is invalid', () => {
        const noBody = { mock: { calls: [['/url', { headers: {} }]] } };
        expect(() => mockedFetchFirstInitBodyString(noBody)).toThrow(
            'Expected fetch mock RequestInit with string body',
        );
        const badBody = { mock: { calls: [['/url', { body: 1 }]] } };
        expect(() => mockedFetchFirstInitBodyString(badBody)).toThrow(
            'Expected fetch mock RequestInit with string body',
        );
    });

    it('mockedFetchFirstInitRecord throws when init is not a record', () => {
        const bad = { mock: { calls: [['/url', 'not-object']] } };
        expect(() => mockedFetchFirstInitRecord(bad)).toThrow('Expected fetch mock RequestInit object');
    });

    it('mockedFetchFirstInitRecord returns init object', () => {
        const init = { method: 'POST', body: '{}' };
        const good = { mock: { calls: [['/url', init]] } };
        expect(mockedFetchFirstInitRecord(good)).toBe(init);
    });
});
