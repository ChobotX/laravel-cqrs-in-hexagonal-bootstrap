import { execInLaravelApp } from './exec-env';

/**
 * PHP prefix: switch tenant connection to `alpha` schema (same pattern as {@link ../../bin/e2e-reset.sh}).
 * Uses `\$` so the host shell does not expand variables inside the Sail command string.
 */
export const alphaTenantTinkerPrefix = String.raw`\$tenant = DB::connection('landlord')->table('tenants')->where('slug', 'alpha')->first(['schema_name']); if (! \$tenant instanceof stdClass) { throw new RuntimeException('Tenant alpha not found.'); } config(['database.connections.tenant.search_path' => \$tenant->schema_name . ',public']); DB::purge('tenant'); DB::reconnect('tenant'); `;

export function execAlphaTenantTinker(phpFragment: string): void {
    execInLaravelApp(`php artisan tinker --execute="${alphaTenantTinkerPrefix}${phpFragment}"`);
}
