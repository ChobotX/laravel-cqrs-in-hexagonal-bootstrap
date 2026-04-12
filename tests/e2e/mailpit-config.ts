import { existsSync } from 'node:fs';

/** Mailpit REST API base (`…/api/v1`). Host: localhost; same Docker Compose network: `mailpit` service. */
export function mailpitApiBase(): string {
    if (process.env.MAILPIT_API_URL !== undefined && process.env.MAILPIT_API_URL !== '') {
        return process.env.MAILPIT_API_URL;
    }
    if (existsSync('/.dockerenv')) {
        return 'http://mailpit:8025/api/v1';
    }
    return 'http://localhost:8025/api/v1';
}
