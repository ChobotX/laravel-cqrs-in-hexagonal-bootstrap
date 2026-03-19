<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Web\Organization\CreateOrganizationRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('organizations.management.create')]
final readonly class CreateOrganizationController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(CreateOrganizationRequest $createOrganizationRequest): RedirectResponse
    {
        $this->commandBus->dispatch($createOrganizationRequest->toCommand());

        return redirect('/organizations')->with('success', __('messages.organizations.created'));
    }
}
