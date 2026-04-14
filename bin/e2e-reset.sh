#!/usr/bin/env bash
# Reset Redis and landlord admin password before Playwright runs.
# On the host: uses Sail. Inside the Laravel app container: `php artisan` only (no nested Docker).
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

RESET_PHP="\$tenant = DB::connection('landlord')->table('tenants')->where('slug', 'alpha')->first(['schema_name']); if (! \$tenant instanceof stdClass) { throw new RuntimeException('Tenant alpha not found.'); } config(['database.connections.tenant.search_path' => \$tenant->schema_name . ',public']); DB::purge('tenant'); DB::reconnect('tenant'); DB::connection('tenant')->table('users')->where('email', 'admin@test.com')->update(['password' => Hash::make('password')]);"

if [[ -f /.dockerenv ]]; then
    php artisan storage:link
    php artisan migrate --database=landlord --path=database/migrations/landlord --force
    php artisan tenant:migrate --tenant=alpha
    php artisan tinker --execute="Illuminate\Support\Facades\Redis::connection()->flushAll();"
    php artisan tinker --execute="${RESET_PHP}"
else
    ./vendor/bin/sail php artisan storage:link
    ./vendor/bin/sail php artisan migrate --database=landlord --path=database/migrations/landlord --force
    ./vendor/bin/sail php artisan tenant:migrate --tenant=alpha
    ./vendor/bin/sail exec redis redis-cli FLUSHALL
    ./vendor/bin/sail php artisan tinker --execute="${RESET_PHP}"
fi
