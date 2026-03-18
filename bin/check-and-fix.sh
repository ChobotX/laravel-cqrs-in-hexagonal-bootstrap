#!/usr/bin/env bash
set -euo pipefail
source "$(dirname "$0")/_common.sh" "$@"

# === Wave 1: Auto-fix (parallel) ===
header "Wave 1: Auto-fix"

frontend_fix() {
    npx biome check --write resources/js/
    npx blade-formatter --write resources/views/**/*.blade.php
}

php_fix() {
    php vendor/bin/rector
}

FE_PID=""
PHP_PID=""

if [[ "$RUN_FRONTEND" -eq 1 ]]; then frontend_fix & FE_PID=$!; fi
if [[ "$RUN_BACKEND" -eq 1 ]]; then php_fix & PHP_PID=$!; fi

if [[ -n "$FE_PID" ]]; then
    if wait "$FE_PID"; then pass "Frontend fix"; else fail "Frontend fix"; FAILED=1; fi
fi
if [[ -n "$PHP_PID" ]]; then
    if wait "$PHP_PID"; then pass "PHP refactor"; else fail "PHP refactor"; FAILED=1; fi
fi

# === Wave 2: Pint fix (after rector) ===
if [[ "$RUN_BACKEND" -eq 1 ]]; then
    header "Wave 2: Pint fix"
    if php vendor/bin/pint; then pass "Pint fix"; else fail "Pint fix"; FAILED=1; fi
fi

# === Wave 3: Check (parallel) ===
header "Wave 3: Check"

frontend_check() {
    bash bin/lint-blade-no-js.sh
    bash bin/lint-blade-a11y.sh
    npx vitest run --coverage
    npx vite build
}

php_check() {
    php vendor/bin/pest --configuration=phpunit.coverage.xml --coverage --min=100
    php vendor/bin/phpstan analyse --memory-limit=512M
}

FE_PID=""
PHP_PID=""

if [[ "$RUN_FRONTEND" -eq 1 ]]; then frontend_check & FE_PID=$!; fi
if [[ "$RUN_BACKEND" -eq 1 ]]; then php_check & PHP_PID=$!; fi

if [[ -n "$FE_PID" ]]; then
    if wait "$FE_PID"; then pass "Frontend checks"; else fail "Frontend checks"; FAILED=1; fi
fi
if [[ -n "$PHP_PID" ]]; then
    if wait "$PHP_PID"; then pass "PHP checks"; else fail "PHP checks"; FAILED=1; fi
fi

result
