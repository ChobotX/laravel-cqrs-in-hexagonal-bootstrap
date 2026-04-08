<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Tenancy\Query\GetTenantSettings\GetTenantSettingsQuery;
use Illuminate\Support\Facades\Context;
use Illuminate\View\View;

#[RequiresPermission('settings.tenant.read')]
final readonly class ShowTenantSettingsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $tenantId = Context::get('tenant_id');
        assert(is_string($tenantId));

        $tenantSettings = $this->queryBus->dispatch(new GetTenantSettingsQuery(
            tenantId: $tenantId,
        ));

        return view('settings.tenant', [
            'settings' => $tenantSettings,
        ]);
    }
}
