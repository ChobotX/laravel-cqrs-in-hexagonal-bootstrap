<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Sso\Contract\Command\DeleteSsoConfigurationCommand;
use Illuminate\Http\RedirectResponse;

use function redirect;

#[RequiresPermission('sso.management.delete')]
final readonly class DeleteSsoConfigurationController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $id): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteSsoConfigurationCommand(id: $id));

        return redirect()->route('settings.sso.index')->with('success', __('messages.sso.deleted'));
    }
}
