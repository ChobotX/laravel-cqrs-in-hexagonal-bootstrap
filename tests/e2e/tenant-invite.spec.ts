import { test, expect, type BrowserContext, type Page } from '@playwright/test';
import { execSync } from 'node:child_process';

// ── Mailpit helpers ─────────────────────────────────────────────────────────

const MAILPIT_API = process.env.MAILPIT_API_URL ?? 'http://localhost:8025/api/v1';

async function clearMailpit(): Promise<void> {
    await fetch(`${MAILPIT_API}/messages`, { method: 'DELETE' });
}

async function waitForMailpitMessage(recipientEmail: string): Promise<string> {
    for (let attempt = 0; attempt < 20; attempt++) {
        const res = await fetch(`${MAILPIT_API}/messages`);
        const data = (await res.json()) as {
            messages?: Array<{ ID: string; To: Array<{ Address: string }> }>;
        };
        const msg = data.messages?.find((m) =>
            m.To.some((to) => to.Address === recipientEmail),
        );
        if (msg) return msg.ID;
        await new Promise<void>((r) => setTimeout(r, 500));
    }
    throw new Error(`Invite email for ${recipientEmail} did not arrive within 10 seconds`);
}

async function extractInviteLinkFromEmail(messageId: string): Promise<string> {
    const res = await fetch(`${MAILPIT_API}/message/${messageId}`);
    const data = (await res.json()) as { HTML?: string; Text?: string };
    const body = data.HTML ?? data.Text ?? '';
    const match = body.match(/https?:\/\/[^"'\s<>]+\/invite\/[^"'\s<>]+/);
    if (!match) throw new Error('Could not find invite link in email body');
    return match[0].replace(/&amp;/g, '&');
}

// ── Sail helpers ─────────────────────────────────────────────────────────────

function sail(command: string): void {
    execSync(`./vendor/bin/sail ${command}`, { stdio: 'inherit', timeout: 30_000 });
}

function processQueue(tenantSlug: string): void {
    // Set APP_URL to the tenant's domain so signed URLs (invite links) point to the correct host.
    // The queue worker runs in CLI context where APP_URL defaults to http://localhost.
    const tenantUrl = `https://${tenantSlug}.laravel-bootstrap.local`;
    sail(
        `php artisan tinker --execute="` +
            `app(App\\\\Contract\\\\Tenancy\\\\TenantBootstrapper::class)->bootstrapBySlug('${tenantSlug}');` +
            `config(['app.url' => '${tenantUrl}']);` +
            `URL::forceRootUrl('${tenantUrl}');` +
            `Artisan::call('queue:work', ['--stop-when-empty' => true, '--queue' => 'default']);"`,
    );
}

/**
 * Idempotent cleanup: drop the e2e tenant schema and landlord records.
 * Safe to call even when the tenant does not exist yet.
 */
function cleanupE2eTenant(): void {
    sail(
        `php artisan tinker --execute="` +
            `DB::connection('landlord')->statement('DROP SCHEMA IF EXISTS \\\"tenant_e2etest\\\" CASCADE');` +
            `DB::connection('landlord')->table('tenant_domains')->where('domain','e2etest')->delete();` +
            `DB::connection('landlord')->table('tenants')->where('slug','e2etest')->delete();` +
            `"`,
    );
}

// ── Constants ────────────────────────────────────────────────────────────────

const ROOT_URL = 'https://laravel-bootstrap.local';
const TENANT_DOMAIN = 'e2etest';
const TENANT_URL = `https://${TENANT_DOMAIN}.laravel-bootstrap.local`;

const ADMIN_NAME = 'E2E Admin';
const ADMIN_EMAIL = 'e2e-admin@example.com';
const MEMBER_NAME = 'E2E Member';
const MEMBER_EMAIL = 'e2e-member@example.com';
const PASSWORD = 'SecurePass123!';

// ── Tests ────────────────────────────────────────────────────────────────────

test.describe('Tenant Registration & Member Invitation', () => {
    test.describe.configure({ mode: 'serial' });
    test.use({ storageState: { cookies: [], origins: [] } });

    let memberInviteLink: string;
    let adminContext: BrowserContext;
    let adminPage: Page;

    test.beforeAll(async () => {
        // Idempotent cleanup from any previous run
        cleanupE2eTenant();
        await clearMailpit();
    });

    test.afterAll(async () => {
        if (adminContext) await adminContext.close();
        cleanupE2eTenant();
        await clearMailpit();
    });

    test('registers a new tenant with admin', async ({ page }) => {
        await page.goto(`${ROOT_URL}/register`);

        await page.getByTestId('register-name-input').fill('E2E Test Corp');
        await page.getByTestId('register-slug-input').fill(TENANT_DOMAIN);
        await page.getByTestId('register-domain-input').fill(TENANT_DOMAIN);
        await page.getByTestId('register-admin-name-input').fill(ADMIN_NAME);
        await page.getByTestId('register-admin-email-input').fill(ADMIN_EMAIL);
        await page.getByTestId('register-submit-button').click();

        await expect(page).toHaveURL(new RegExp(`${TENANT_DOMAIN}\\.laravel-bootstrap\\.local/login`));
        await expect(page.getByTestId('login-submit-button')).toBeVisible();
    });

    test('admin accepts invite and logs in', async ({ browser }) => {
        processQueue(TENANT_DOMAIN);

        const messageId = await waitForMailpitMessage(ADMIN_EMAIL);
        // Force HTTPS: the URL is signed with relative path (scheme-independent),
        // but the page must load over HTTPS to avoid mixed-content session issues.
        const adminInviteLink = (await extractInviteLinkFromEmail(messageId)).replace(
            /^http:\/\//,
            'https://',
        );

        expect(adminInviteLink).toContain('/invite/');
        expect(adminInviteLink).toContain('signature=');

        adminContext = await browser.newContext({
            storageState: { cookies: [], origins: [] },
            ignoreHTTPSErrors: true,
        });
        adminPage = await adminContext.newPage();

        await adminPage.goto(adminInviteLink);
        await expect(adminPage.getByTestId('invite-password-input')).toBeVisible();

        await adminPage.getByTestId('invite-password-input').fill(PASSWORD);
        await adminPage.getByTestId('invite-password-confirmation-input').fill(PASSWORD);

        await adminPage.getByTestId('invite-submit-button').click();

        await expect(adminPage).toHaveURL(/\/users/);
        await expect(adminPage.locator('#app-flash-data[data-success]')).toHaveAttribute(
            'data-success',
            /./,
        );
        await expect(adminPage.getByTestId('topbar-user-email')).toHaveText(ADMIN_EMAIL);
    });

    test('admin invites a new member', async () => {
        await clearMailpit();

        await adminPage.goto(`${TENANT_URL}/users/create`);
        await expect(adminPage).toHaveURL(/\/users\/create/);

        await adminPage.getByTestId('user-create-name-input').fill(MEMBER_NAME);
        await adminPage.getByTestId('user-create-email-input').fill(MEMBER_EMAIL);
        await adminPage.getByTestId('user-create-submit-button').click();

        await expect(adminPage).toHaveURL(/\/users/);
        await expect(adminPage.locator('#app-flash-data[data-success]')).toHaveAttribute(
            'data-success',
            /./,
        );

        processQueue(TENANT_DOMAIN);

        const messageId = await waitForMailpitMessage(MEMBER_EMAIL);
        memberInviteLink = (await extractInviteLinkFromEmail(messageId)).replace(
            /^http:\/\//,
            'https://',
        );

        expect(memberInviteLink).toContain('/invite/');
        expect(memberInviteLink).toContain('signature=');
    });

    test('invited member accepts and logs in', async ({ browser }) => {
        const memberContext = await browser.newContext({
            storageState: { cookies: [], origins: [] },
            ignoreHTTPSErrors: true,
        });
        const memberPage = await memberContext.newPage();

        await memberPage.goto(memberInviteLink);
        await expect(memberPage.getByTestId('invite-password-input')).toBeVisible();

        await memberPage.getByTestId('invite-password-input').fill(PASSWORD);
        await memberPage.getByTestId('invite-password-confirmation-input').fill(PASSWORD);
        await memberPage.getByTestId('invite-submit-button').click();

        // Member has no role yet, so /users returns 403. Navigate to profile to verify login.
        await memberPage.goto(`${TENANT_URL}/profile`);
        await expect(memberPage).toHaveURL(/\/profile/);
        await expect(memberPage.getByTestId('topbar-user-email')).toHaveText(MEMBER_EMAIL);

        await memberContext.close();
    });
});
