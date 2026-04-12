import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { expect, test as setup } from '@playwright/test';

const authFile = join(dirname(fileURLToPath(import.meta.url)), '.auth/user.json');

setup('authenticate as admin', async ({ page }) => {
    await page.goto('/login');

    await page.getByTestId('login-email-input').fill('admin@test.com');
    await page.getByTestId('login-password-input').fill('password');
    await page.getByTestId('login-submit-button').click();

    await page.waitForURL('**/users');
    await expect(page.getByTestId('topbar-user-email')).toBeVisible();

    await page.context().storageState({ path: authFile });
});
