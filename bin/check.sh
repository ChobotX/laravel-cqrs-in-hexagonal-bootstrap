#!/usr/bin/env bash
set -euo pipefail
source "$(dirname "$0")/_common.sh" "$@"

# === Wave 1: Static analysis & linting (parallel) ===
wave "Wave 1 · Static analysis & linting"

frontend_lint() {
    run_step "blade-formatter" npx blade-formatter --check-formatted resources/views/**/*.blade.php
    run_step "blade-no-js" bash bin/lint-blade-no-js.sh
    run_step "blade-a11y" bash bin/lint-blade-a11y.sh
    run_step "blade-url" bash bin/lint-blade-url.sh
    run_step "blade-layers" bash bin/lint-blade-layers.sh
    run_step "biome" npx biome check resources/js/ tests/e2e/
    run_step "catch-blocks" bash bin/lint-catch-blocks.sh
    run_step "frontend-structure" bash bin/lint-frontend-structure.sh
    run_step "depcruise" npx depcruise resources/js/app.ts --config --output-type err-long
    run_step "vitest" npx vitest run --coverage
    run_step "vite-build" npx vite build
}

php_lint() {
    run_step "pint" php vendor/bin/pint --test
    run_step "rector" php vendor/bin/rector --dry-run
    run_step "phpstan" php vendor/bin/phpstan analyse --memory-limit=512M
}

if [[ "$RUN_FRONTEND" -eq 1 ]]; then run_group fe frontend_lint & FE_PID=$!; else FE_PID=""; fi
if [[ "$RUN_BACKEND" -eq 1 ]]; then run_group php php_lint & PHP_PID=$!; else PHP_PID=""; fi

[[ -n "$FE_PID" ]] && wait "$FE_PID"
[[ -n "$PHP_PID" ]] && wait "$PHP_PID"

[[ -n "$FE_PID" ]] && replay_group fe
[[ -n "$PHP_PID" ]] && replay_group php

# === Wave 2: Tests & coverage (parallel) ===
if [[ "$RUN_BACKEND" -eq 1 ]]; then
    wave "Wave 2 · Tests & coverage"

    tests() {
        run_step "pest" php vendor/bin/pest --parallel
    }

    coverage() {
        run_step "coverage · domain" php vendor/bin/pest --configuration=phpunit.domain-coverage.xml --coverage --min=100
        run_step "coverage · presentation" php vendor/bin/pest --configuration=phpunit.presentation-coverage.xml --coverage --min=100
        run_step "coverage · infrastructure" php vendor/bin/pest --configuration=phpunit.infrastructure-coverage.xml --coverage --min=100
    }

    run_group tests tests & T_PID=$!
    run_group cov coverage & COV_PID=$!

    wait "$T_PID"
    wait "$COV_PID"

    replay_group tests
    replay_group cov
fi

summary
