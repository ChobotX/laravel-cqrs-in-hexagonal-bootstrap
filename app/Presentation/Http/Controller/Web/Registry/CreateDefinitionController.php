<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Domain\Registry\Contract\Command\CreateDefinitionCommand;
use App\Presentation\Http\Request\Web\Registry\CreateDefinitionRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.definitions.create')]
final readonly class CreateDefinitionController
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateDefinitionRequest $createDefinitionRequest): RedirectResponse
    {
        $this->commandBus->dispatch(new CreateDefinitionCommand(
            id: $this->idGenerator->generate(),
            namespace: $createDefinitionRequest->namespace(),
            slug: $createDefinitionRequest->slug(),
            name: $createDefinitionRequest->name(),
        ));

        return redirect()->route('registry.definitions.show', [
            $createDefinitionRequest->namespace(),
            $createDefinitionRequest->slug(),
        ])->with('success', __('messages.registry.definitions.created'));
    }
}
