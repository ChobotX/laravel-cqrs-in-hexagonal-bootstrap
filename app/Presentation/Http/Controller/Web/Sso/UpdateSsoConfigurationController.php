<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
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

    public function __invoke(UpdateSsoConfigurationRequest $updateSsoConfigurationRequest, string $id): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateSsoConfigurationCommand(
            id: $id,
            displayName: $updateSsoConfigurationRequest->displayName(),
            enabled: $updateSsoConfigurationRequest->enabled(),
            enforce: $updateSsoConfigurationRequest->enforce(),
            jitMode: $updateSsoConfigurationRequest->jitMode(),
            allowedEmailDomains: $updateSsoConfigurationRequest->allowedEmailDomains(),
            config: $updateSsoConfigurationRequest->configMap(),
        ));

        return redirect()->route('settings.sso.index')->with('success', __('messages.sso.updated'));
    }
}
