#!/usr/bin/env bash
# Reset Redis and landlord admin password before Playwright runs.
# On the host: uses Sail. Inside the Laravel app container: `php artisan` only (no nested Docker).
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

RESET_PHP="app(App\\Contract\\Tenancy\\TenantBootstrapper::class)->bootstrapBySlug('alpha'); DB::table('users')->where('email','admin@test.com')->update(['password' => Hash::make('password')]);"

if [[ -f /.dockerenv ]]; then
    php artisan tinker --execute="Illuminate\Support\Facades\Redis::connection()->flushAll();"
    php artisan tinker --execute="${RESET_PHP}"
else
    ./vendor/bin/sail exec redis redis-cli FLUSHALL
    ./vendor/bin/sail php artisan tinker --execute="${RESET_PHP}"
fi
