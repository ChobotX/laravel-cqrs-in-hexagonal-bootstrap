import { test, expect } from '@playwright/test';

test.describe('Profile — Password Change', () => {
    test.describe.configure({ mode: 'serial' });

    test.beforeEach(async ({ page }) => {
        await page.goto('/profile');
        await expect(page).toHaveURL(/\/profile/);
    });

    test('shows error when current password is missing', async ({ page }) => {
        await page.getByTestId('profile-password-input').fill('newpassword123');
        await page.getByTestId('profile-password-confirmation-input').fill('newpassword123');
        await page.getByTestId('profile-save-button').click();

        await expect(page.getByTestId('profile-current-password-error')).toBeVisible();
    });

    test('shows error when current password is wrong', async ({ page }) => {
        await page.getByTestId('profile-current-password-input').fill('wrongpassword');
        await page.getByTestId('profile-password-input').fill('newpassword123');
        await page.getByTestId('profile-password-confirmation-input').fill('newpassword123');
        await page.getByTestId('profile-save-button').click();

        await expect(page.getByTestId('profile-current-password-error')).toBeVisible();
    });

    test('shows error when new password is too short', async ({ page }) => {
        await page.getByTestId('profile-current-password-input').fill('password');
        await page.getByTestId('profile-password-input').fill('short');
        await page.getByTestId('profile-password-confirmation-input').fill('short');
        await page.getByTestId('profile-save-button').click();

        await expect(page.getByTestId('profile-password-error')).toBeVisible();
    });

    test('shows error when password confirmation does not match', async ({ page }) => {
        await page.getByTestId('profile-current-password-input').fill('password');
        await page.getByTestId('profile-password-input').fill('newpassword123');
        await page.getByTestId('profile-password-confirmation-input').fill('differentvalue');
        await page.getByTestId('profile-save-button').click();

        await expect(page.getByTestId('profile-password-error')).toBeVisible();
    });

    test('successful password change shows flash and allows revert', async ({ page }) => {
        await page.getByTestId('profile-current-password-input').fill('password');
        await page.getByTestId('profile-password-input').fill('newpassword123');
        await page.getByTestId('profile-password-confirmation-input').fill('newpassword123');
        await page.getByTestId('profile-save-button').click();

        await expect(page).toHaveURL(/\/profile/);
        await expect(page.locator('#app-flash-data[data-success]')).toHaveAttribute(
            'data-success',
            /./,
        );

        // Revert password back to 'password' for test isolation
        await page.getByTestId('profile-current-password-input').fill('newpassword123');
        await page.getByTestId('profile-password-input').fill('password');
        await page.getByTestId('profile-password-confirmation-input').fill('password');
        await page.getByTestId('profile-save-button').click();

        await expect(page).toHaveURL(/\/profile/);
    });
});
