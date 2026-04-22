<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Contract\IdGenerator;
use App\Domain\Registry\Contract\Command\CreateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Presentation\Http\Request\Web\Registry\CreateDefinitionVersionRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.definitions.update')]
final readonly class CreateDefinitionVersionController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateDefinitionVersionRequest $createDefinitionVersionRequest, string $namespace, string $slug): RedirectResponse
    {
        /** @var Definition|null $definition */
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        if ($definition === null) {
            abort(HttpStatus::NOT_FOUND);
        }

        $this->commandBus->dispatch(new CreateDefinitionVersionCommand(
            id: $this->idGenerator->generate(),
            definitionId: $definition->id->value,
            schemaData: $createDefinitionVersionRequest->schema(),
        ));

        return redirect()->back()->with('success', __('messages.registry.versions.created'));
    }
}
