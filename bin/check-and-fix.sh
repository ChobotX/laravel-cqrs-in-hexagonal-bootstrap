#!/usr/bin/env bash
set -euo pipefail
source "$(dirname "$0")/_common.sh" "$@"

# === Wave 1: Auto-fix (parallel) ===
wave "Wave 1 · Auto-fix"

frontend_fix() {
    run_step "biome-fix" npx biome check --write resources/js/
    run_step "blade-formatter-fix" npx blade-formatter --write resources/views/**/*.blade.php
}

php_fix() {
    run_step "rector-fix" php vendor/bin/rector
}

if [[ "$RUN_FRONTEND" -eq 1 ]]; then run_group fe_fix frontend_fix & FE_PID=$!; else FE_PID=""; fi
if [[ "$RUN_BACKEND" -eq 1 ]]; then run_group php_fix php_fix & PHP_PID=$!; else PHP_PID=""; fi

[[ -n "$FE_PID" ]] && wait "$FE_PID"
[[ -n "$PHP_PID" ]] && wait "$PHP_PID"

[[ -n "$FE_PID" ]] && replay_group fe_fix
[[ -n "$PHP_PID" ]] && replay_group php_fix

# === Wave 2: Pint fix (after rector) ===
if [[ "$RUN_BACKEND" -eq 1 ]]; then
    wave "Wave 2 · Pint fix"
    run_step "pint-fix" php vendor/bin/pint
fi

# === Wave 3: Check (parallel) ===
wave "Wave 3 · Check"

frontend_check() {
    run_step "blade-no-js" bash bin/lint-blade-no-js.sh
    run_step "blade-a11y" bash bin/lint-blade-a11y.sh
    run_step "blade-url" bash bin/lint-blade-url.sh
    run_step "blade-layers" bash bin/lint-blade-layers.sh
    run_step "catch-blocks" bash bin/lint-catch-blocks.sh
    run_step "frontend-structure" bash bin/lint-frontend-structure.sh
    run_step "depcruise" npx depcruise resources/js/app.ts --config --output-type err-long
    run_step "vitest" npx vitest run --coverage
    run_step "vite-build" npx vite build
}

php_check() {
    run_step "pest-coverage" php vendor/bin/pest --configuration=phpunit.coverage.xml --coverage --min=100
    run_step "phpstan" php vendor/bin/phpstan analyse --memory-limit=512M
}

if [[ "$RUN_FRONTEND" -eq 1 ]]; then run_group fe_check frontend_check & FE_PID=$!; else FE_PID=""; fi
if [[ "$RUN_BACKEND" -eq 1 ]]; then run_group php_check php_check & PHP_PID=$!; else PHP_PID=""; fi

[[ -n "$FE_PID" ]] && wait "$FE_PID"
[[ -n "$PHP_PID" ]] && wait "$PHP_PID"

[[ -n "$FE_PID" ]] && replay_group fe_check
[[ -n "$PHP_PID" ]] && replay_group php_check

summary
