const HTTP_UNAUTHORIZED = 401;
const HTTP_CSRF_EXPIRED = 419;
const SESSION_EXPIRED_CODES: Set<number> = new Set([HTTP_UNAUTHORIZED, HTTP_CSRF_EXPIRED]);

const originalFetch: typeof window.fetch = window.fetch;

window.fetch = async (input: RequestInfo | URL, init?: RequestInit): Promise<Response> => {
    const response = await originalFetch.call(window, input, init);

    if (!SESSION_EXPIRED_CODES.has(response.status)) {
        return response;
    }

    if (window.location.pathname === '/login') {
        return response;
    }

    const requestUrl = new URL(input instanceof Request ? input.url : input.toString(), window.location.origin);
    if (requestUrl.origin !== window.location.origin) {
        return response;
    }

    const loginUrl = new URL('/login', window.location.origin);
    loginUrl.searchParams.set('redirect', window.location.pathname + window.location.search);
    window.location.replace(loginUrl.toString());

    return response;
};
