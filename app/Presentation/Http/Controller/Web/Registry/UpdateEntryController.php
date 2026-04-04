<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Command\UpdateEntry\UpdateEntryCommand;
use App\Domain\Registry\Query\GetEntryById\GetEntryByIdQuery;
use App\Presentation\Http\Request\Web\Registry\UpdateEntryRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.entries.update')]
final readonly class UpdateEntryController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(UpdateEntryRequest $updateEntryRequest, string $namespace, string $slug, string $id): RedirectResponse
    {
        $entry = $this->queryBus->dispatch(new GetEntryByIdQuery($id));

        abort_unless($entry !== null, HttpStatus::NOT_FOUND);

        $this->commandBus->dispatch(new UpdateEntryCommand(
            id: $id,
            title: $updateEntryRequest->title(),
            data: $updateEntryRequest->entryData(),
        ));

        return redirect()->route('registry.entries.index', [
            'namespace' => $namespace,
            'slug' => $slug,
        ])->with('success', __('messages.registry.entries.updated'));
    }
}
