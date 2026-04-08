<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\View\View;

#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(string $namespace, string $slug): View
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $currentUserId = $this->authenticatedUser->id() ?? '';
        $presetShareCapabilities = $this->queryBus->dispatch(new GetPresetShareCapabilitiesQuery($currentUserId));

        return view('registry.entries.index', [
            'definition' => $definition,
            'namespace' => $namespace,
            'slug' => $slug,
            'canShareTeam' => $presetShareCapabilities->canShareTeam,
            'canShareGlobal' => $presetShareCapabilities->canShareGlobal,
            'shareableTeams' => $presetShareCapabilities->shareableTeams,
        ]);
    }
}
