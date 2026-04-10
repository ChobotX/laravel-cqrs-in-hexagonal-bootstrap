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

Each test gets its own browser context. No state leaks between tests. Don't rely on test execution order.

### Determinism

- Use absolute fixtures: seeded user emails (`admin@test.com`), fixed passwords (`password`)
- Never generate random data in tests
- Avoid asserting on locale-dependent text — use `data-testid` + visibility assertions instead
- No `page.waitForTimeout()` — use web-first assertions or `page.waitForURL()` which auto-retry

### Performance

- Tests run in parallel by default (`fullyParallel: true`)
- Auth is set up once and reused via `storageState` — no per-test login overhead
- Only Chromium is configured (add Firefox/WebKit when cross-browser testing is needed)

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
├── auth.spec.ts                # Login/logout tests
├── profile-password.spec.ts    # Profile password change tests
└── README.md                   # This file
```
