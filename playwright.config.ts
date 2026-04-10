import { defineConfig, devices } from '@playwright/test';
import { execSync } from 'node:child_process';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const baseURL = process.env.E2E_BASE_URL ?? 'https://alpha.laravel-bootstrap.local';

// Chromium's DNS resolver doesn't support mDNS (.local domains resolved by
// OrbStack/Bonjour). Resolve the IP via macOS dscacheutil and pass it as a
// host-resolver-rule so Chromium can reach the app.
function resolveHostForChromium(url: string): string[] {
    const hostname = new URL(url).hostname;
    try {
        const out = execSync(`dscacheutil -q host -a name ${hostname}`, { encoding: 'utf-8' });
        const match = out.match(/ip_address: (.+)/);
        if (match) return [`--host-resolver-rules=MAP ${hostname} ${match[1]}`];
    } catch { /* standard DNS works, no override needed */ }
    return [];
}

export default defineConfig({
    testDir: 'tests/e2e',
    fullyParallel: true,
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
            name: 'chromium',
            use: {
                ...devices['Desktop Chrome'],
                storageState: join(__dirname, 'tests/e2e/.auth/user.json'),
            },
            dependencies: ['setup'],
            teardown: 'teardown',
        },
        { name: 'teardown', testMatch: /.*\.teardown\.ts/ },
    ],
});
