import { expect, test } from '@playwright/test';

test.describe('SSO', () => {
    test.describe('Admin UI', () => {
        test('lists configured providers and lets the admin add one', async ({ page }) => {
            await page.goto('/settings/sso');

            await expect(page).toHaveURL(/\/settings\/sso/);

            await page.getByTestId('sso-create-link').click();
            await expect(page).toHaveURL(/\/settings\/sso\/create/);

            await page.getByTestId('sso-provider-type').selectOption('oidc');
            await page.getByTestId('sso-slug').fill('e2e-okta');
            await page.getByTestId('sso-display-name').fill('E2E Okta');
            await page.getByTestId('sso-jit-mode').selectOption('invited_only');
            await page.getByTestId('sso-allowed-email-domains').fill('example.com');
            await page.getByTestId('sso-enabled').check();
            await page.getByTestId('sso-submit').click();

            await expect(page).toHaveURL(/\/settings\/sso/);
            await expect(page.getByText('E2E Okta')).toBeVisible();
        });
    });

    test.describe('Login page', () => {
        test.use({ storageState: { cookies: [], origins: [] } });

        test('does not show SSO buttons when feature flag is off (default)', async ({ page }) => {
            await page.goto('/login');

            await expect(page.getByTestId('login-submit-button')).toBeVisible();
        });
    });
});
