<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Contract\IdGenerator;
use App\Domain\Registry\Command\CreateEntry\CreateEntryCommand;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Presentation\Http\Request\Web\Registry\CreateEntryRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.entries.create')]
final readonly class CreateEntryController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateEntryRequest $request, string $namespace, string $slug): RedirectResponse
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $this->commandBus->dispatch(new CreateEntryCommand(
            id: $this->idGenerator->generate(),
            definitionId: $definition->id->value,
            title: $request->title(),
            data: $request->entryData(),
        ));

        return redirect()->route('registry.entries.index', [
            'namespace' => $namespace,
            'slug' => $slug,
        ])->with('success', __('messages.registry.entries.created'));
    }
}
