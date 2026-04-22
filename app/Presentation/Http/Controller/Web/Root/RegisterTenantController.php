<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Root;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\IdGenerator;
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
        $registerTenantWithAdminCommand = $registerTenantFormRequest->toCommand($this->idGenerator->generate());

        $this->commandBus->dispatch($registerTenantWithAdminCommand);

        /** @var string $rootDomain */
        $rootDomain = config('tenancy.root_domain');

        return redirect($registerTenantFormRequest->getScheme().'://'.$registerTenantWithAdminCommand->domain.'.'.$rootDomain.'/login');
    }
}
