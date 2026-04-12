# E2E Tests (Playwright)

Browser-based end-to-end tests that verify user-facing flows against a running application. Complements Pest (backend) and Vitest (frontend unit/component) tests.

## Prerequisites

1. Sail running: `./vendor/bin/sail up -d`
2. Database seeded: `./vendor/bin/sail php artisan tenant:setup`
3. Chromium installed: `npx playwright install chromium`

## Running Tests

```bash
npm run test:e2e          # Headless, all tests
npm run test:e2e:ui       # Interactive UI mode (debugging)
npx playwright test --headed  # See the browser
npx playwright test tests/e2e/auth.spec.ts  # Single file
npx playwright show-report    # Open HTML report
```

Configure `E2E_BASE_URL` to override the default base URL (`https://alpha.laravel-bootstrap.local`):

```bash
E2E_BASE_URL=https://bravo.laravel-bootstrap.local npm run test:e2e
```

E2E tests are **not** part of `composer check` — they require a running app with seeded data and a host-side browser.

## TypeScript linting (Biome)

`tests/e2e/**/*.ts` is included in [`biome.json`](../../biome.json) and linted with the rest of the frontend in the Biome step of `composer check` / `composer lint:ts` (same paths as `resources/js/`).

**Narrow overrides** for e2e only (not a full opt-out): `useExplicitType`, `noAwaitInLoops`, and `noMagicNumbers` are turned off so Playwright specs stay readable. The **no-type-assertion** plugin stays **on** — avoid `(await response.json()) as { … }` and similar; use small helpers that narrow from `unknown` (for example [`mailpit-json.ts`](mailpit-json.ts) for Mailpit API payloads).

Import aliases such as `import { test as setup }` are not type assertions and are unaffected.

## Authentication

The `auth.setup.ts` file logs in as `admin@test.com` / `password` once and saves the session to `.auth/user.json`. All tests in the `chromium` project start pre-authenticated.

For tests that need an unauthenticated (guest) context:

```typescript
test.describe('Guest flow', () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test('shows login page', async ({ page }) => {
        await page.goto('/login');
        // ...
    });
});
```

## Writing Tests — Best Practices

### Selectors

Always use `data-testid` attributes via `page.getByTestId()`. This is the project-wide convention (used across Blade and Vue components).

**Naming convention**: `{page/context}-{element}-{type}`

Examples: `login-email-input`, `login-submit-button`, `topbar-user-email`, `users-create-button`

**Blade components**: Use the `testId` prop (available on `primary-button`, `topbar-button`):

```blade
<x-primary-button testId="users-create-button" ... />
```

For plain HTML elements, add the attribute directly:

```blade
<input data-testid="login-email-input" ... />
```

**Vue components**: Use `:data-testid` or `data-testid` directly on elements.

### Assertions

Use web-first assertions — they auto-retry until the condition is met or timeout:

```typescript
// Good — auto-retries
await expect(page).toHaveURL(/\/users/);
await expect(page.getByTestId('topbar-user-email')).toBeVisible();

// Bad — does not retry, races with the browser
expect(page.url()).toContain('/users');
```

### No Force Clicks

Never pass `{ force: true }` to click or other actions. If an element isn't clickable, the test is catching a real accessibility/UX issue — fix the UI, not the test.

### Test Isolation

Each test gets its own browser context by default. For multi-step flows that share state (e.g., password change + revert, tenant registration + invite acceptance), use `test.describe.configure({ mode: 'serial' })` — within those describes, order is intentional.

Tests that mutate shared state (e.g., password) must include `afterAll` cleanup that resets via artisan, so subsequent projects start from a known state.

### Determinism

- Use absolute fixtures: seeded user emails (`admin@test.com`), fixed passwords (`password`)
- Never generate random data in tests
- Avoid asserting on locale-dependent text — use `data-testid` + visibility assertions instead
- No `page.waitForTimeout()` — use web-first assertions or `page.waitForURL()` which auto-retry

### Execution Order

Tests run in sequential project phases via Playwright dependencies (see `playwright.config.ts`):

```
setup → auth-tests → profile-tests → tenant-tests → teardown
```

Why sequential instead of parallel:
- Shared admin user (`admin@test.com`) is rate-limited at 5 req/min on login
- Profile tests mutate admin password (change + revert)
- Tenant tests create/destroy schemas affecting server-side state

As the suite grows, tests using independent users and non-shared endpoints can be grouped into parallel projects.

### Performance

- Auth is set up once and reused via `storageState` — no per-test login overhead
- Only Chromium is configured (add Firefox/WebKit when cross-browser testing is needed)
- `test:e2e:reset` runs [`bin/e2e-reset.sh`](../../bin/e2e-reset.sh) — Redis flush + admin password reset (Sail on host, `php artisan` inside the app container so `npm run test:e2e` works from `./vendor/bin/sail npm run test:e2e`)

## Mailpit Integration

For tests that verify email delivery (e.g., invite acceptance), use Mailpit's REST API. Prefer the typed helpers in [`mailpit-json.ts`](mailpit-json.ts) (list messages, fetch message detail, extract HTML) so response bodies are narrowed from `unknown` instead of using inline `as` shapes.

Use [`mailpit-config.ts`](mailpit-config.ts) `mailpitApiBase()` (or set `MAILPIT_API_URL`) so Mailpit resolves to `localhost` on the host and to the `mailpit` service when Playwright runs inside the Sail app container. Example flow:

```typescript
import { mailpitApiBase } from './mailpit-config';
import { parseMailpitMessageBody, parseMailpitMessagesResponse } from './mailpit-json';

const MAILPIT_API = mailpitApiBase();

// Clear messages before test
await fetch(`${MAILPIT_API}/messages`, { method: 'DELETE' });

// Poll for a message by recipient
const listRes = await fetch(`${MAILPIT_API}/messages`);
const list = parseMailpitMessagesResponse(await listRes.json());
const msg = list.messages?.find((m) => m.To.some((to) => to.Address === 'user@example.com'));

// Fetch full message with HTML body
const detailRes = await fetch(`${MAILPIT_API}/message/${msg.ID}`);
const body = parseMailpitMessageBody(await detailRes.json());
```

Since domain events are queued (`HandleDomainEventJob`), process the queue before polling Mailpit. From specs, use `execInLaravelApp` from [`exec-env.ts`](exec-env.ts) so the same tests run on the host (via Sail) and inside the app container (bare `php artisan`, no nested Docker):

```typescript
import { execInLaravelApp } from './exec-env';

execInLaravelApp('php artisan queue:work --stop-when-empty --queue=default');
```

## Debugging

- **UI mode**: `npm run test:e2e:ui` — step through tests, inspect DOM, see timeline
- **Traces**: Collected on first retry. Open with `npx playwright show-trace <path>`
- **Screenshots**: Captured on failure, saved to `test-results/`
- **HTML report**: `npx playwright show-report`

## File Structure

```
tests/e2e/
├── .auth/                      # Saved auth state (gitignored)
│   └── user.json
├── auth.setup.ts               # Login setup — runs before all tests
├── auth.teardown.ts            # Cleanup stored auth state after tests
├── auth.spec.ts                # Login/logout tests
├── exec-env.ts                 # Artisan / shell: Sail on host, `php` in app container
├── mailpit-config.ts           # Mailpit API base URL (host vs in-container)
├── mailpit-json.ts             # Mailpit API response narrowing (no inline json() casts)
├── profile-password.spec.ts    # Profile password change tests
├── tenant-invite.spec.ts       # Tenant registration + member invitation
└── README.md                   # This file
```
