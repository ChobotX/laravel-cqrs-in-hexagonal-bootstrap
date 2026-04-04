<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\Command\UpdateDefinition\UpdateDefinitionCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Presentation\Http\Request\Web\Registry\UpdateDefinitionRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.definitions.update')]
final readonly class UpdateDefinitionController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(UpdateDefinitionRequest $updateDefinitionRequest, string $namespace, string $slug): RedirectResponse
    {
        /** @var Definition|null $definition */
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        if ($definition === null) {
            abort(HttpStatus::NOT_FOUND);
        }

        $this->commandBus->dispatch(new UpdateDefinitionCommand(
            id: $definition->id->value,
            name: $updateDefinitionRequest->name(),
        ));

        return redirect()->back()->with('success', __('messages.registry.definitions.updated'));
    }
}
