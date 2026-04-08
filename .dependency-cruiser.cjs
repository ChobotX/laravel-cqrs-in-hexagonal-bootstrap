/** @type {import('dependency-cruiser').IConfiguration} */
module.exports = {
    forbidden: [
        // ── Rule 1: core/ must not import from shared/, behaviors/, or widgets/ ──
        // Core is the foundation layer — zero upward dependencies.
        // Parallels backend testContractHasNoAppDependencies.
        {
            name: 'no-core-upward-deps',
            comment: 'core/ must not import from shared/, behaviors/, or widgets/.',
            severity: 'error',
            from: {
                path: '^resources/js/core/',
            },
            to: {
                path: '^resources/js/(shared|behaviors|widgets)/',
            },
        },

        // ── Rule 2: behaviors/ must not import from shared/ or widgets/ ──────────
        // Vanilla DOM scripts are standalone. They communicate via DOM globals.
        {
            name: 'no-behaviors-upward-deps',
            comment:
                'behaviors/ must not import from shared/ or widgets/. ' +
                'Use DOM globals (window.appDialog, etc.) for cross-module communication.',
            severity: 'error',
            from: {
                path: '^resources/js/behaviors/',
            },
            to: {
                path: '^resources/js/(shared|widgets)/',
            },
        },

        // ── Rule 3: shared/ must not import from widgets/ or behaviors/ ──────────
        // Shared services may use core/ but never depend on feature widgets.
        // Parallels backend testApplicationDependsOnlyOnDomainAndContract.
        {
            name: 'no-shared-upward-deps',
            comment: 'shared/ must not import from widgets/ or behaviors/.',
            severity: 'error',
            from: {
                path: '^resources/js/shared/',
            },
            to: {
                path: '^resources/js/(widgets|behaviors)/',
            },
        },

        // ── Rule 4: No cross-widget imports ──────────────────────────────────────
        // Feature widgets must not import from sibling widgets.
        // Parallels backend NoCrossDomainDependenciesRule.
        {
            name: 'no-cross-widget-imports',
            comment:
                'widgets/X/ must not import from widgets/Y/. ' +
                'Use shared/ services or DOM globals for cross-widget communication.',
            severity: 'error',
            from: {
                path: '^resources/js/widgets/([^/]+)/',
            },
            to: {
                path: '^resources/js/widgets/([^/]+)/',
                pathNot: '^resources/js/widgets/$1/',
            },
        },

        // ── Rule 5: No Vue imports in core/ or behaviors/ ───────────────────────
        // Only widgets/ and shared/ may use Vue.
        {
            name: 'no-vue-in-core-or-behaviors',
            comment: 'core/ and behaviors/ must not import Vue — they are framework-free.',
            severity: 'error',
            from: {
                path: '^resources/js/(core|behaviors)/',
            },
            to: {
                path: 'node_modules/vue/',
            },
        },

        // ── Rule 6: No circular dependencies ────────────────────────────────────
        {
            name: 'no-circular',
            comment: 'No circular dependencies allowed.',
            severity: 'error',
            from: {},
            to: {
                circular: true,
            },
        },
    ],
    options: {
        doNotFollow: {
            path: 'node_modules',
        },
        tsPreCompilationDeps: true,
        enhancedResolveOptions: {
            exportsFields: ['exports'],
            conditionNames: ['import', 'require', 'node', 'default'],
            extensions: ['.ts', '.vue', '.js', '.json'],
        },
        reporterOptions: {
            text: {
                highlightFocused: true,
            },
        },
        parser: 'tsc',
        cache: {
            strategy: 'content',
            folder: 'node_modules/.cache/dependency-cruiser',
        },
    },
};
