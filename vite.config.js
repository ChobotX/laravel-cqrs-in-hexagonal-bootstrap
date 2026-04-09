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
                'resources/js/**/*.vue',
                'resources/js/**/types.ts',
                'resources/js/types/**',
                // Vite compile-time macro (import.meta.glob produces {} in vitest)
                'resources/js/shared/i18n/i18n.ts',
                // Third-party SDK bootstrap
                'resources/js/core/sentry.ts',
                // Mount-point wiring (createApp + mount, no testable logic)
                'resources/js/app.ts',
                'resources/js/**/*-app.ts',
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
