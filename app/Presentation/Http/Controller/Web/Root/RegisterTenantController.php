<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Root;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Tenancy\Command\CreateTenant\CreateTenantCommand;
use App\Presentation\Http\Request\Root\RegisterTenantFormRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('Public tenant registration')]
final readonly class RegisterTenantController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(RegisterTenantFormRequest $registerTenantFormRequest): RedirectResponse
    {
        $slug = $registerTenantFormRequest->string('slug')->toString();
        $domain = $registerTenantFormRequest->string('domain')->toString();

        $this->commandBus->dispatch(new CreateTenantCommand(
            name: $registerTenantFormRequest->string('name')->toString(),
            slug: $slug,
            domain: $domain,
        ));

        /** @var string $rootDomain */
        $rootDomain = config('tenancy.root_domain');

        $tenantUrl = $registerTenantFormRequest->getScheme().'://'.$domain.'.'.$rootDomain.'/login';

        return redirect($tenantUrl);
    }
}
