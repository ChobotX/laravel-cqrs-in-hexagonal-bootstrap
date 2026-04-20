<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Sso\Contract\Command\UpdateSsoConfigurationCommand;
use App\Presentation\Http\Request\Sso\UpdateSsoConfigurationRequest;
use Illuminate\Http\RedirectResponse;

use function redirect;

#[RequiresPermission('sso.management.update')]
final readonly class UpdateSsoConfigurationController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateSsoConfigurationRequest $request, string $id): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateSsoConfigurationCommand(
            id: $id,
            displayName: $request->displayName(),
            enabled: $request->enabled(),
            enforce: $request->enforce(),
            jitMode: $request->jitMode(),
            allowedEmailDomains: $request->allowedEmailDomains(),
            config: $request->configMap(),
        ));

        return redirect()->route('settings.sso.index')->with('success', __('messages.sso.updated'));
    }
}
