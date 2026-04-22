<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class ListRolesController
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

        return view('roles.index', [
            'canUpdate' => $this->authorizationChecker->can($currentUserId, 'users.roles.update'),
            'canShareTeam' => $presetShareCapabilities->canShareTeam,
            'canShareGlobal' => $presetShareCapabilities->canShareGlobal,
            'shareableTeams' => $presetShareCapabilities->shareableTeams,
        ]);
    }
}
