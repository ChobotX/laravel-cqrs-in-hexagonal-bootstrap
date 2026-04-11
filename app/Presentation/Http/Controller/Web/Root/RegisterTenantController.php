<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Root;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Presentation\Http\Request\Root\RegisterTenantFormRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('Public tenant registration')]
final readonly class RegisterTenantController
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
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

        $this->commandBus->dispatch(new InitializeTenantAdminCommand(
            tenantSlug: $slug,
            adminId: $this->idGenerator->generate(),
            adminName: $registerTenantFormRequest->string('admin_name')->toString(),
            adminEmail: $registerTenantFormRequest->string('admin_email')->toString(),
        ));

        /** @var string $rootDomain */
        $rootDomain = config('tenancy.root_domain');

        $tenantUrl = $registerTenantFormRequest->getScheme().'://'.$domain.'.'.$rootDomain.'/login';

        return redirect($tenantUrl);
    }
}
