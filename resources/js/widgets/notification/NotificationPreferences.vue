<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { ref } from 'vue';
import { NotificationLevel, type NotificationPreference } from './notification-store';

const props = defineProps<{
    initialPreferences: NotificationPreference[];
}>();

const preferences = ref<NotificationPreference[]>(
    props.initialPreferences.length > 0
        ? props.initialPreferences.map((p) => ({ ...p }))
        : [
              { level: NotificationLevel.Info, inApp: true, email: false },
              { level: NotificationLevel.Success, inApp: true, email: false },
              { level: NotificationLevel.Warning, inApp: true, email: true },
              { level: NotificationLevel.Error, inApp: true, email: true },
          ],
);

function levelLabel(level: NotificationLevel): string {
    return trans(`messages.notifications.levels.${level}`);
}
</script>

<template>
    <div data-testid="notification-preferences">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="pb-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ trans('messages.notifications.preferences_level') }}
                    </th>
                    <th class="pb-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ trans('messages.notifications.preferences_in_app') }}
                    </th>
                    <th class="pb-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ trans('messages.notifications.preferences_email') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr
                    v-for="pref in preferences"
                    :key="pref.level"
                    :data-testid="`pref-row-${pref.level}`"
                >
                    <td class="py-2.5 text-sm text-gray-700">{{ levelLabel(pref.level) }}</td>
                    <td class="py-2.5 text-center">
                        <input
                            type="checkbox"
                            checked
                            disabled
                            class="cursor-not-allowed rounded border-gray-300 text-indigo-600 opacity-50"
                            :data-testid="`pref-in-app-${pref.level}`"
                        />
                    </td>
                    <td class="py-2.5 text-center">
                        <input
                            v-model="pref.email"
                            type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                            :data-testid="`pref-email-${pref.level}`"
                        />
                        <input
                            type="hidden"
                            :name="`notification_preferences[${pref.level}][email]`"
                            :value="pref.email ? '1' : '0'"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
