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
    public function __invoke(RegisterTenantFormRequest $request, TenantMigrator $migrator): RedirectResponse
    {
        /** @var string $dbHost */
        $dbHost = config('database.connections.tenant.host');

        /** @var int $dbPort */
        $dbPort = (int) config('database.connections.tenant.port');

        /** @var string $dbName */
        $dbName = config('database.connections.tenant.database');

        /** @var string $dbUser */
        $dbUser = config('database.connections.tenant.username');

        /** @var string $dbPass */
        $dbPass = config('database.connections.tenant.password');

        $slug = $request->string('slug')->toString();

        $tenant = TenantModel::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->string('name')->toString(),
            'slug' => $slug,
            'schema_name' => 'tenant_'.$slug,
            'database_host' => $dbHost,
            'database_port' => $dbPort,
            'database_name' => $dbName,
            'database_username' => $dbUser,
            'database_password' => $dbPass,
            'is_active' => true,
            'config' => [],
        ]);

        $domain = $request->string('domain')->toString();

        TenantDomainModel::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenant->id,
            'domain' => $domain,
            'is_primary' => true,
        ]);

        $migrator->setupTenant($tenant);

        /** @var string $rootDomain */
        $rootDomain = config('tenancy.root_domain');

        $tenantUrl = $request->getScheme().'://'.$domain.'.'.$rootDomain.'/login';

        return redirect($tenantUrl);
    }
}
