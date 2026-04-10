import { test, expect } from '@playwright/test';

test.describe('Authentication', () => {
    test.describe('Login', () => {
        test.use({ storageState: { cookies: [], origins: [] } });

        test('successful login redirects to users page', async ({ page }) => {
            await page.goto('/login');

            await page.getByTestId('login-email-input').fill('admin@test.com');
            await page.getByTestId('login-password-input').fill('admin');
            await page.getByTestId('login-submit-button').click();

            await expect(page).toHaveURL(/\/users/);
            await expect(page.getByTestId('topbar-user-email')).toBeVisible();
        });

        test('failed login shows error message', async ({ page }) => {
            await page.goto('/login');

            await page.getByTestId('login-email-input').fill('admin@test.com');
            await page.getByTestId('login-password-input').fill('wrongpassword');
            await page.getByTestId('login-submit-button').click();

            await expect(page).toHaveURL(/\/login/);
            await expect(page.getByTestId('login-error-message')).toBeVisible();
        });
    });

    test.describe('Logout', () => {
        test('logout redirects to login page', async ({ page }) => {
            await page.goto('/users');

            await expect(page.getByTestId('topbar-user-email')).toBeVisible();
            await page.getByTestId('logout-button').click();

            await expect(page).toHaveURL(/\/login/);
        });
    });
});
