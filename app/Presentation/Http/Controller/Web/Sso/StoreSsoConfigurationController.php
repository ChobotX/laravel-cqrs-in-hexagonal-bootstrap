<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Domain\Sso\Contract\Command\ConfigureSsoConfigurationCommand;
use App\Presentation\Http\Request\Sso\StoreSsoConfigurationRequest;
use Illuminate\Http\RedirectResponse;

use function redirect;

#[RequiresPermission('sso.management.create')]
final readonly class StoreSsoConfigurationController
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(StoreSsoConfigurationRequest $storeSsoConfigurationRequest): RedirectResponse
    {
        $this->commandBus->dispatch(new ConfigureSsoConfigurationCommand(
            id: $this->idGenerator->generate(),
            providerType: $storeSsoConfigurationRequest->providerType(),
            slug: $storeSsoConfigurationRequest->slug(),
            displayName: $storeSsoConfigurationRequest->displayName(),
            enabled: $storeSsoConfigurationRequest->enabled(),
            enforce: $storeSsoConfigurationRequest->enforce(),
            jitMode: $storeSsoConfigurationRequest->jitMode(),
            allowedEmailDomains: $storeSsoConfigurationRequest->allowedEmailDomains(),
            config: $storeSsoConfigurationRequest->configMap(),
        ));

        return redirect()->route('settings.sso.index')->with('success', __('messages.sso.created'));
    }
}
