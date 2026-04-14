import { expect, test } from '@playwright/test';
import { execAlphaTenantTinker } from './alpha-tenant-tinker';
import { mailpitApiBase } from './mailpit-config';
import { parseMailpitMessageBody, parseMailpitMessagesResponse } from './mailpit-json';
import { totpCode } from './totp-code';

const MAILPIT_API: string = mailpitApiBase();

const PASSWORD = 'password';
const USER_TOTP = 'e2e-2fa-totp@test.com';
const USER_EMAIL = 'e2e-2fa-email@test.com';

test.describe.configure({ mode: 'serial' });

test.describe('Two-factor authentication', () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test.beforeAll(() => {
        execAlphaTenantTinker(
            "DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update(['required_for_all_users' => true]);",
        );
    });

    test.afterAll(() => {
        execAlphaTenantTinker(
            "DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update(['required_for_all_users' => false]); " +
                "DB::connection('tenant')->table('email_two_factor_challenges')->join('users', 'email_two_factor_challenges.user_id', '=', 'users.id')->whereIn('users.email', ['e2e-2fa-totp@test.com', 'e2e-2fa-email@test.com'])->delete(); " +
                "DB::connection('tenant')->table('users')->whereIn('email', ['e2e-2fa-totp@test.com', 'e2e-2fa-email@test.com'])->update(['email_two_factor_enabled' => false, 'email_two_factor_confirmed_at' => null, 'totp_secret' => null, 'totp_confirmed_at' => null, 'totp_recovery_code_hashes' => null]);",
        );
    });

    test('TOTP: enroll from settings then complete login challenge', async ({ page }) => {
        await page.goto('/login');
        await page.getByTestId('login-email-input').fill(USER_TOTP);
        await page.getByTestId('login-password-input').fill(PASSWORD);
        await page.getByTestId('login-submit-button').click();

        await expect(page).toHaveURL(/\/profile\/two-factor/);

        await page.getByTestId('own-two-factor-totp-switch').click();
        await expect(page.getByTestId('own-two-factor-totp-qr')).toBeVisible();

        await page.getByTestId('own-two-factor-totp-secret-summary').click();
        const secretText = await page.getByTestId('own-two-factor-totp-secret').textContent();
        expect(secretText).toBeTruthy();
        const secret = secretText?.trim() ?? '';

        const downloadPromise = page.waitForEvent('download');
        await page.getByTestId('own-two-factor-totp-backup-download').click();
        await downloadPromise;

        await expect(page.getByTestId('own-two-factor-totp-download-ack')).toBeVisible();

        await page.getByTestId('own-two-factor-totp-code-input').fill(totpCode(secret));
        await page.getByTestId('own-two-factor-totp-confirm-submit').click();

        await expect(page).toHaveURL(/\/users/);
        await expect(page.getByTestId('topbar-user-email')).toBeVisible();

        await page.getByTestId('logout-button').click();
        await expect(page).toHaveURL(/\/login/);

        await page.getByTestId('login-email-input').fill(USER_TOTP);
        await page.getByTestId('login-password-input').fill(PASSWORD);
        await page.getByTestId('login-submit-button').click();

        await expect(page).toHaveURL(/\/two-factor/);
        await page.getByTestId('two-factor-method-select').selectOption('totp');
        await page.getByTestId('two-factor-code-input').fill(totpCode(secret));
        await page.getByTestId('two-factor-verify-submit').click();

        await expect(page).toHaveURL(/\/users/);
        await expect(page.getByTestId('topbar-user-email')).toBeVisible();
    });

    test('Email OTP: enable from settings then complete login challenge via Mailpit', async ({ page }) => {
        await fetch(`${MAILPIT_API}/messages`, { method: 'DELETE' });

        await page.goto('/login');
        await page.getByTestId('login-email-input').fill(USER_EMAIL);
        await page.getByTestId('login-password-input').fill(PASSWORD);
        await page.getByTestId('login-submit-button').click();

        await expect(page).toHaveURL(/\/profile\/two-factor/);

        await page.getByTestId('own-two-factor-email-switch').click();
        await expect(page).toHaveURL(/\/users/);

        await page.getByTestId('logout-button').click();
        await expect(page).toHaveURL(/\/login/);

        await page.getByTestId('login-email-input').fill(USER_EMAIL);
        await page.getByTestId('login-password-input').fill(PASSWORD);
        await page.getByTestId('login-submit-button').click();

        await expect(page).toHaveURL(/\/two-factor/);

        await page.getByTestId('two-factor-send-email-submit').click();
        await expect(page).toHaveURL(/\/two-factor/);

        const messageId = await waitForMailpitMessageTo(USER_EMAIL);
        const code = await extractSixDigitCodeFromMailpit(messageId);

        await page.getByTestId('two-factor-method-select').selectOption('email');
        await page.getByTestId('two-factor-code-input').fill(code);
        await page.getByTestId('two-factor-verify-submit').click();

        await expect(page).toHaveURL(/\/users/);
        await expect(page.getByTestId('topbar-user-email')).toBeVisible();
    });
});

async function waitForMailpitMessageTo(recipientEmail: string): Promise<string> {
    for (let attempt = 0; attempt < 30; attempt++) {
        const res = await fetch(`${MAILPIT_API}/messages`);
        const data = parseMailpitMessagesResponse(await res.json());
        const msg = data.messages?.find((m) => m.To.some((to) => to.Address === recipientEmail));
        if (msg?.ID) {
            return msg.ID;
        }
        await new Promise<void>((resolve) => {
            setTimeout(resolve, 500);
        });
    }
    throw new Error(`No Mailpit message for ${recipientEmail} within 15 seconds`);
}

async function extractSixDigitCodeFromMailpit(messageId: string): Promise<string> {
    const res = await fetch(`${MAILPIT_API}/message/${messageId}`);
    const body = parseMailpitMessageBody(await res.json());
    const text = body.HTML ?? body.Text ?? '';
    const match = text.match(/\b(\d{6})\b/);
    if (!match?.[1]) {
        throw new Error('Could not find 6-digit code in email body');
    }

    return match[1];
}
