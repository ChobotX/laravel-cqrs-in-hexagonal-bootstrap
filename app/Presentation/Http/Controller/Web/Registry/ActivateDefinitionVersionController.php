<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Command\ActivateDefinitionVersion\ActivateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Domain\Registry\Query\ListDefinitionVersions\ListDefinitionVersionsQuery;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('registry.definitions.update')]
final readonly class ActivateDefinitionVersionController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug, int $version): RedirectResponse
    {
        /** @var Definition|null $definition */
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        if ($definition === null) {
            abort(HttpStatus::NOT_FOUND);
        }

        /** @var list<DefinitionVersion> $versions */
        $versions = $this->queryBus->dispatch(new ListDefinitionVersionsQuery($definition->id->value));

        $targetVersion = $this->findVersionByNumber($versions, $version);

        if (! $targetVersion instanceof DefinitionVersion) {
            abort(HttpStatus::NOT_FOUND);
        }

        $this->commandBus->dispatch(new ActivateDefinitionVersionCommand($targetVersion->id->value));

        return redirect()->back()->with('success', __('messages.registry.versions.activated'));
    }

    /** @param list<DefinitionVersion> $versions */
    private function findVersionByNumber(array $versions, int $versionNumber): ?DefinitionVersion
    {
        foreach ($versions as $version) {
            if ($version->version->value === $versionNumber) {
                return $version;
            }
        }

        return null;
    }
}
