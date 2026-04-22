<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Contract\IdGenerator;
use App\Domain\Registry\Contract\Command\CreateEntryCommand;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\Registry\CreateEntryRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.entries.create')]
final readonly class CreateEntryController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(CreateEntryRequest $createEntryRequest, string $namespace, string $slug): RedirectResponse
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $userId = $this->authenticatedUser->id() ?? '';
        abort_if($userId === '', HttpStatus::FORBIDDEN);

        $this->commandBus->dispatch(new CreateEntryCommand(
            id: $this->idGenerator->generate(),
            definitionId: $definition->id->value,
            title: $createEntryRequest->title(),
            data: $createEntryRequest->entryData(),
            createdByUserId: $userId,
        ));

        return redirect()->route('registry.entries.index', [
            'namespace' => $namespace,
            'slug' => $slug,
        ])->with('success', __('messages.registry.entries.created'));
    }
}
