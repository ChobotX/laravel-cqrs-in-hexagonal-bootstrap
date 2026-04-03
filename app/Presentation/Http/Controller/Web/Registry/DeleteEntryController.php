<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Registry\Command\DeleteEntry\DeleteEntryCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.entries.delete')]
final readonly class DeleteEntryController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $namespace, string $slug, string $id): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteEntryCommand($id));

        return redirect()->route('registry.entries.index', [
            'namespace' => $namespace,
            'slug' => $slug,
        ])->with('success', __('messages.registry.entries.deleted'));
    }
}
