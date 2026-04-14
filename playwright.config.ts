import { defineConfig, devices } from '@playwright/test';
import { execSync } from 'node:child_process';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const baseURL = process.env.E2E_BASE_URL ?? 'https://alpha.laravel-bootstrap.local';
const authFile = join(__dirname, 'tests/e2e/.auth/user.json');

// Chromium's DNS resolver doesn't support mDNS (.local domains resolved by
// OrbStack/Bonjour). Resolve the IP via macOS dscacheutil and pass it as a
// host-resolver-rule so Chromium can reach the app.
function resolveHostForChromium(url: string): string[] {
    if (process.platform !== 'darwin') {
        return [];
    }

    const hostname = new URL(url).hostname;

    try {
        const out = execSync(`dscacheutil -q host -a name ${hostname}`, { encoding: 'utf-8' });
        const match = out.match(/ip_address: (.+)/);

        if (match) {
            const ip = match[1].trim();
            const tld = hostname.split('.').slice(1).join('.') || hostname;

            return [`--host-resolver-rules=MAP *.${tld} ${ip},MAP ${tld} ${ip}`];
        }
    } catch {
        // standard DNS works, no override needed
    }

    return [];
}

const chromiumDevice = { ...devices['Desktop Chrome'] };

/*
 * Execution phases (enforced via project dependencies):
 *
 *   setup → auth-tests → profile-tests → settings-password-rotation-tests → tenant-tests → teardown
 *
 * Why sequential phases instead of parallel:
 * - auth-tests and profile-tests share the admin user (admin@test.com),
 *   which is rate-limited at 5 req/min on the login endpoint. Parallel
 *   execution exhausts the limit.
 * - profile-tests mutate the admin password (change + revert). Running in
 *   parallel with auth-tests causes credential mismatches.
 * - settings-password-rotation-tests reuse the authenticated admin session.
 * - tenant-tests create/destroy schemas and run artisan commands that can
 *   affect server-side connection state.
 *
 * As the suite grows, tests that use independent users and don't share
 * rate-limited endpoints can be grouped into parallel projects.
 */
export default defineConfig({
    testDir: 'tests/e2e',
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: process.env.CI
        ? [['list'], ['html', { open: 'never' }]]
        : [['html', { open: 'never' }], ['list']],
    use: {
        baseURL,
        testIdAttribute: 'data-testid',
        ignoreHTTPSErrors: true,
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        launchOptions: {
            args: resolveHostForChromium(baseURL),
        },
    },
    projects: [
        { name: 'setup', testMatch: /.*\.setup\.ts/ },
        {
            name: 'auth-tests',
            use: { ...chromiumDevice, storageState: authFile },
            testMatch: /auth\.spec\.ts$/,
            dependencies: ['setup'],
        },
        {
            name: 'profile-tests',
            use: { ...chromiumDevice, storageState: authFile },
            testMatch: /profile-password\.spec\.ts$/,
            dependencies: ['auth-tests'],
        },
        {
            name: 'settings-password-rotation-tests',
            use: { ...chromiumDevice, storageState: authFile },
            testMatch: /password-rotation-settings\.spec\.ts$/,
            dependencies: ['profile-tests'],
        },
        {
            name: 'tenant-tests',
            use: { ...chromiumDevice, storageState: authFile },
            testMatch: /tenant-invite\.spec\.ts$/,
            dependencies: ['settings-password-rotation-tests'],
            teardown: 'teardown',
        },
        { name: 'teardown', testMatch: /.*\.teardown\.ts/ },
    ],
});
