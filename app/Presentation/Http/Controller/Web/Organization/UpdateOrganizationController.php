<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Web\Organization\UpdateOrganizationRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('organizations.management.update')]
final readonly class UpdateOrganizationController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateOrganizationRequest $updateOrganizationRequest): RedirectResponse
    {
        $this->commandBus->dispatch($updateOrganizationRequest->toCommand());

        return redirect('/organizations')->with('success', __('messages.organizations.updated'));
    }
}
