import { sentryVitePlugin } from '@sentry/vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import * as compiler from 'vue/compiler-sfc';

const vuePlugin = process.env.VITEST
    ? vue({
          compiler: {
              ...compiler,
              compileTemplate: (...args) => ({
                  ...compiler.compileTemplate(...args),
                  map: undefined,
              }),
          },
      })
    : vue();

const enableSentrySourcemaps = !!process.env.SENTRY_AUTH_TOKEN;

export default defineConfig({
    build: {
        sourcemap: enableSentrySourcemaps ? 'hidden' : false,
    },
    plugins: [
        tailwindcss(),
        vuePlugin,
        i18n(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        ...(enableSentrySourcemaps
            ? [
                  sentryVitePlugin({
                      org: process.env.SENTRY_ORG,
                      project: process.env.SENTRY_PROJECT,
                      authToken: process.env.SENTRY_AUTH_TOKEN,
                      sourcemaps: {
                          filesToDeleteAfterUpload: ['./public/build/**/*.map'],
                      },
                  }),
              ]
            : []),
    ],
    test: {
        environment: 'jsdom',
        include: ['resources/js/**/*.test.ts'],
        coverage: {
            provider: 'v8',
            include: ['resources/js/**/*.{ts,vue}'],
            exclude: [
                'resources/js/**/*.test.ts',
                'resources/js/types/**',
                'resources/js/app.ts',
                'resources/js/core/sentry.ts',
                'resources/js/shared/dialog/dialog-app.ts',
                'resources/js/shared/toast/toast-app.ts',
                'resources/js/widgets/chip-selector/chip-selector-app.ts',
                'resources/js/widgets/autocomplete/autocomplete-app.ts',
                'resources/js/widgets/team-tree/team-tree-app.ts',
                'resources/js/**/*.vue',
                'resources/js/widgets/data-grid/composables/types.ts',
                'resources/js/widgets/schema-builder/types.ts',
                'resources/js/behaviors/team-tree-toggle.ts',
                'resources/js/widgets/notification/notification-bell-app.ts',
                'resources/js/widgets/notification/notification-list-app.ts',
                'resources/js/widgets/notification/notification-preferences-app.ts',
                'resources/js/widgets/schema-form/schema-form-app.ts',
                'resources/js/widgets/schema-builder/schema-builder-app.ts',
                'resources/js/widgets/data-grid/pages/users-grid-app.ts',
                'resources/js/widgets/data-grid/pages/teams-grid-app.ts',
                'resources/js/widgets/data-grid/pages/roles-grid-app.ts',
                'resources/js/widgets/data-grid/pages/entries-grid-app.ts',
                'resources/js/widgets/data-grid/pages/feature-flags-grid-app.ts',
            ],
            thresholds: {
                autoUpdate: true,
                statements: 100,
                branches: 100,
                functions: 100,
                lines: 100,
            },
        },
    },
});
