#!/usr/bin/env bash
# Reset Redis and landlord admin password before Playwright runs.
# On the host: uses Sail. Inside the Laravel app container: `php artisan` only (no nested Docker).
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

RESET_PHP="\$tenant = DB::connection('landlord')->table('tenants')->where('slug', 'alpha')->first(['schema_name']); if (! \$tenant instanceof stdClass) { throw new RuntimeException('Tenant alpha not found.'); } config(['database.connections.tenant.search_path' => \$tenant->schema_name . ',public']); DB::purge('tenant'); DB::reconnect('tenant'); DB::connection('tenant')->table('users')->where('email', 'admin@test.com')->update(['password' => Hash::make('password')]); DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->update(['required_for_all_users' => false]); \$e2eEmails = ['e2e-2fa-totp@test.com', 'e2e-2fa-email@test.com']; \$e2eIds = DB::connection('tenant')->table('users')->whereIn('email', \$e2eEmails)->pluck('id'); DB::connection('tenant')->table('email_two_factor_challenges')->whereIn('user_id', \$e2eIds)->delete(); DB::connection('tenant')->table('users')->whereIn('email', \$e2eEmails)->update(['email_two_factor_enabled' => false, 'email_two_factor_confirmed_at' => null, 'totp_secret' => null, 'totp_confirmed_at' => null, 'totp_recovery_code_hashes' => null]);"

if [[ -f /.dockerenv ]]; then
    php artisan storage:link --force
    php artisan migrate --database=landlord --path=database/migrations/landlord --force
    php artisan tenant:migrate --tenant=alpha
    php artisan db:seed --force --class=Database\\Seeders\\E2eAlphaTwoFactorUsersSeeder
    php artisan tinker --execute="Illuminate\Support\Facades\Redis::connection()->flushAll();"
    php artisan tinker --execute="${RESET_PHP}"
else
    ./vendor/bin/sail php artisan storage:link --force
    ./vendor/bin/sail php artisan migrate --database=landlord --path=database/migrations/landlord --force
    ./vendor/bin/sail php artisan tenant:migrate --tenant=alpha
    ./vendor/bin/sail php artisan db:seed --force --class=Database\\Seeders\\E2eAlphaTwoFactorUsersSeeder
    ./vendor/bin/sail exec redis redis-cli FLUSHALL
    ./vendor/bin/sail php artisan tinker --execute="${RESET_PHP}"
fi
