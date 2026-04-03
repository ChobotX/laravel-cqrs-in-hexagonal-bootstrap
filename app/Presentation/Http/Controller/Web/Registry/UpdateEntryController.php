<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Registry\Command\UpdateEntry\UpdateEntryCommand;
use App\Presentation\Http\Request\Web\Registry\UpdateEntryRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.entries.update')]
final readonly class UpdateEntryController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateEntryRequest $request, string $namespace, string $slug, string $id): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateEntryCommand(
            id: $id,
            title: $request->title(),
            data: $request->entryData(),
        ));

        return redirect()->route('registry.entries.index', [
            'namespace' => $namespace,
            'slug' => $slug,
        ])->with('success', __('messages.registry.entries.updated'));
    }
}
