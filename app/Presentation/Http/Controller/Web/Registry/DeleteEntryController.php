<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\Command\DeleteEntryCommand;
use App\Domain\Registry\Contract\Query\GetEntryByIdQuery;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.entries.delete')]
final readonly class DeleteEntryController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug, string $id): RedirectResponse
    {
        $entry = $this->queryBus->dispatch(new GetEntryByIdQuery($id));

        abort_unless($entry !== null, HttpStatus::NOT_FOUND);

        $this->commandBus->dispatch(new DeleteEntryCommand($id));

        return redirect()->route('registry.entries.index', [
            'namespace' => $namespace,
            'slug' => $slug,
        ])->with('success', __('messages.registry.entries.deleted'));
    }
}
