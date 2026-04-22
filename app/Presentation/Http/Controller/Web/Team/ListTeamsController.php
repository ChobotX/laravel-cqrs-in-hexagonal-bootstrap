<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsController
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';
        $presetShareCapabilities = $this->queryBus->dispatch(new GetPresetShareCapabilitiesQuery($currentUserId));

        return view('teams.index', [
            'canCreate' => $this->authorizationChecker->can($currentUserId, 'teams.management.create'),
            'canRead' => true,
            'canUpdate' => $this->authorizationChecker->can($currentUserId, 'teams.management.update'),
            'canDelete' => $this->authorizationChecker->can($currentUserId, 'teams.management.delete'),
            'canShareTeam' => $presetShareCapabilities->canShareTeam,
            'canShareGlobal' => $presetShareCapabilities->canShareGlobal,
            'shareableTeams' => $presetShareCapabilities->shareableTeams,
        ]);
    }
}
