#!/usr/bin/env bash
set -euo pipefail
source "$(dirname "$0")/_common.sh" "$@"

# === Wave 1: Static analysis & linting (parallel) ===
header "Wave 1: Static analysis & linting"

frontend_lint() {
    npx blade-formatter --check-formatted resources/views/**/*.blade.php
    bash bin/lint-blade-no-js.sh
    bash bin/lint-blade-a11y.sh
    npx biome check resources/js/
    npx vitest run --coverage
    npx vite build
}

php_lint() {
    php vendor/bin/pint --test
    php vendor/bin/rector --dry-run
    php vendor/bin/phpstan analyse --memory-limit=512M
}

FE_PID=""
PHP_PID=""

if [[ "$RUN_FRONTEND" -eq 1 ]]; then frontend_lint & FE_PID=$!; fi
if [[ "$RUN_BACKEND" -eq 1 ]]; then php_lint & PHP_PID=$!; fi

if [[ -n "$FE_PID" ]]; then
    if wait "$FE_PID"; then pass "Frontend lint"; else fail "Frontend lint"; FAILED=1; fi
fi
if [[ -n "$PHP_PID" ]]; then
    if wait "$PHP_PID"; then pass "PHP lint"; else fail "PHP lint"; FAILED=1; fi
fi

# === Wave 2: Tests & coverage (parallel) ===
if [[ "$RUN_BACKEND" -eq 1 ]]; then
    header "Wave 2: Tests & coverage"

    tests() {
        php vendor/bin/pest --parallel
    }

    # Coverage processes run sequentially within one job to avoid
    # racing each other for the shared 'testing' database.
    coverage() {
        local failed=0
        if php vendor/bin/pest --configuration=phpunit.domain-coverage.xml --coverage --min=100; then
            pass "Domain coverage"
        else
            fail "Domain coverage"; failed=1
        fi
        if php vendor/bin/pest --configuration=phpunit.presentation-coverage.xml --coverage --min=100; then
            pass "Presentation coverage"
        else
            fail "Presentation coverage"; failed=1
        fi
        if php vendor/bin/pest --configuration=phpunit.infrastructure-coverage.xml --coverage --min=100; then
            pass "Infrastructure coverage"
        else
            fail "Infrastructure coverage"; failed=1
        fi
        return $failed
    }

    tests & T_PID=$!
    coverage & COV_PID=$!

    if wait $T_PID; then pass "Tests"; else fail "Tests"; FAILED=1; fi
    if ! wait $COV_PID; then FAILED=1; fi
fi

result
