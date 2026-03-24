<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Root;

use App\Application\Authorization\SkipPermissionCheck;
use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Presentation\Http\Request\Root\RegisterTenantFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

#[SkipPermissionCheck('Public tenant registration')]
final readonly class RegisterTenantController
{
    public function __invoke(RegisterTenantFormRequest $registerTenantFormRequest, TenantMigrator $tenantMigrator): RedirectResponse
    {
        /** @var array{host: string, port: string|int, database: string, username: string, password: string} $cfg */
        $cfg = config('database.connections.tenant');

        $slug = $registerTenantFormRequest->string('slug')->toString();

        $tenant = TenantModel::create([
            'id' => Str::uuid()->toString(),
            'name' => $registerTenantFormRequest->string('name')->toString(),
            'slug' => $slug,
            'schema_name' => 'tenant_'.$slug,
            'database_host' => $cfg['host'],
            'database_port' => (int) $cfg['port'],
            'database_name' => $cfg['database'],
            'database_username' => $cfg['username'],
            'database_password' => $cfg['password'],
            'is_active' => true,
            'config' => [],
        ]);

        $domain = $registerTenantFormRequest->string('domain')->toString();

        TenantDomainModel::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenant->id,
            'domain' => $domain,
            'is_primary' => true,
        ]);

        $tenantMigrator->setupTenant($tenant);

        /** @var string $rootDomain */
        $rootDomain = config('tenancy.root_domain');

        $tenantUrl = $registerTenantFormRequest->getScheme().'://'.$domain.'.'.$rootDomain.'/login';

        return redirect($tenantUrl);
    }
}
