<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Organization\Command\DeleteOrganization\DeleteOrganizationCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('organizations.management.delete')]
final readonly class DeleteOrganizationController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $organizationId): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteOrganizationCommand($organizationId));

        return redirect('/organizations')->with('success', __('messages.organizations.deleted'));
    }
}
