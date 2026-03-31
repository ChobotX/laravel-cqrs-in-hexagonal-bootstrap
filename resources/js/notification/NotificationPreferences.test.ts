import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import NotificationPreferences from './NotificationPreferences.vue';
import { NotificationLevel, type NotificationPreference } from './notification-store';

vi.mock('laravel-vue-i18n', () => ({
    trans: (key: string): string => key,
}));

function mountPrefs(initialPreferences: NotificationPreference[] = []): ReturnType<typeof mount> {
    return mount(NotificationPreferences, {
        props: { initialPreferences },
    });
}

describe('NotificationPreferences', () => {
    it('renders 4 level rows', () => {
        const wrapper = mountPrefs();

        expect(wrapper.find('[data-testid="pref-row-info"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="pref-row-success"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="pref-row-warning"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="pref-row-error"]').exists()).toBe(true);
    });

    it('uses default preferences when none provided', () => {
        const wrapper = mountPrefs([]);
        const infoEmail = wrapper.find<HTMLInputElement>('[data-testid="pref-email-info"]');
        const warningEmail = wrapper.find<HTMLInputElement>('[data-testid="pref-email-warning"]');
        const errorEmail = wrapper.find<HTMLInputElement>('[data-testid="pref-email-error"]');

        expect(infoEmail.element.checked).toBe(false);
        expect(warningEmail.element.checked).toBe(true);
        expect(errorEmail.element.checked).toBe(true);
    });

    it('uses provided preferences', () => {
        const prefs: NotificationPreference[] = [
            { level: NotificationLevel.Info, inApp: true, email: true },
            { level: NotificationLevel.Success, inApp: true, email: true },
            { level: NotificationLevel.Warning, inApp: true, email: false },
            { level: NotificationLevel.Error, inApp: true, email: false },
        ];
        const wrapper = mountPrefs(prefs);

        const infoEmail = wrapper.find<HTMLInputElement>('[data-testid="pref-email-info"]');
        const warningEmail = wrapper.find<HTMLInputElement>('[data-testid="pref-email-warning"]');

        expect(infoEmail.element.checked).toBe(true);
        expect(warningEmail.element.checked).toBe(false);
    });

    it('has in-app checkboxes always checked and disabled', () => {
        const wrapper = mountPrefs();

        for (const level of ['info', 'success', 'warning', 'error']) {
            const checkbox = wrapper.find<HTMLInputElement>(`[data-testid="pref-in-app-${level}"]`);
            expect(checkbox.element.checked).toBe(true);
            expect(checkbox.element.disabled).toBe(true);
        }
    });

    it('toggles email checkbox', async () => {
        const wrapper = mountPrefs();
        const infoEmail = wrapper.find<HTMLInputElement>('[data-testid="pref-email-info"]');

        expect(infoEmail.element.checked).toBe(false);

        await infoEmail.setValue(true);

        expect(infoEmail.element.checked).toBe(true);
    });

    it('outputs hidden inputs with correct name and value', () => {
        const prefs: NotificationPreference[] = [
            { level: NotificationLevel.Info, inApp: true, email: true },
            { level: NotificationLevel.Success, inApp: true, email: false },
            { level: NotificationLevel.Warning, inApp: true, email: true },
            { level: NotificationLevel.Error, inApp: true, email: false },
        ];
        const wrapper = mountPrefs(prefs);

        const infoHidden = wrapper.find<HTMLInputElement>('input[name="notification_preferences[info][email]"]');
        const successHidden = wrapper.find<HTMLInputElement>('input[name="notification_preferences[success][email]"]');

        expect(infoHidden.element.value).toBe('1');
        expect(successHidden.element.value).toBe('0');
    });

    it('renders column headers', () => {
        const wrapper = mountPrefs();

        expect(wrapper.text()).toContain('messages.notifications.preferences_level');
        expect(wrapper.text()).toContain('messages.notifications.preferences_in_app');
        expect(wrapper.text()).toContain('messages.notifications.preferences_email');
    });

    it('renders level labels via translation', () => {
        const wrapper = mountPrefs();

        expect(wrapper.text()).toContain('messages.notifications.levels.info');
        expect(wrapper.text()).toContain('messages.notifications.levels.success');
        expect(wrapper.text()).toContain('messages.notifications.levels.warning');
        expect(wrapper.text()).toContain('messages.notifications.levels.error');
    });
});
