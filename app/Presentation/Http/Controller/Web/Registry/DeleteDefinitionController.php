<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Command\DeleteDefinition\DeleteDefinitionCommand;
use App\Domain\Registry\Definition;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.definitions.delete')]
final readonly class DeleteDefinitionController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug): RedirectResponse
    {
        /** @var Definition|null $definition */
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        if ($definition === null) {
            abort(HttpStatus::NOT_FOUND);
        }

        $this->commandBus->dispatch(new DeleteDefinitionCommand($definition->id->value));

        return redirect()->route('registry.definitions.index')->with('success', __('messages.registry.definitions.deleted'));
    }
}
