import { expect, test } from '@playwright/test';

test.describe('Settings — Password rotation', () => {
    test('admin can open password rotation settings and save', async ({ page }) => {
        await page.goto('/settings?tab=password-rotation');

        await expect(page).toHaveURL(/\/settings\?tab=password-rotation/);
        await expect(page.getByTestId('password-rotation-title')).toBeVisible();

        await page.getByTestId('password-rotation-save-button').click();

        await expect(page).toHaveURL(/\/settings\?tab=password-rotation/);
        await expect(page.locator('#app-flash-data[data-success]')).toHaveAttribute('data-success', /./);
    });
});
