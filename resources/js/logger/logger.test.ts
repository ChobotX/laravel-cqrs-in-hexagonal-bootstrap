import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { debug, error, info, warn } from './logger';

describe('logger', () => {
    beforeEach(() => {
        vi.spyOn(console, 'debug').mockImplementation(() => undefined);
        vi.spyOn(console, 'info').mockImplementation(() => undefined);
        vi.spyOn(console, 'warn').mockImplementation(() => undefined);
        vi.spyOn(console, 'error').mockImplementation(() => undefined);
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('debug delegates to console.debug', () => {
        debug('test message', { key: 'value' });

        expect(console.debug).toHaveBeenCalledWith('test message', { key: 'value' });
    });

    it('info delegates to console.info', () => {
        info('test message', 'extra-context');

        expect(console.info).toHaveBeenCalledWith('test message', 'extra-context');
    });

    it('warn delegates to console.warn', () => {
        warn('test message');

        expect(console.warn).toHaveBeenCalledWith('test message');
    });

    it('error delegates to console.error', () => {
        error('test message', new Error('fail'));

        expect(console.error).toHaveBeenCalledWith('test message', new Error('fail'));
    });
});
