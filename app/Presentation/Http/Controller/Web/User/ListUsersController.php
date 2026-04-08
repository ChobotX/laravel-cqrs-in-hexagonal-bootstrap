<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\View\View;

#[RequiresPermission('users.list.read')]
final readonly class ListUsersController
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

        return view('users.index', [
            'canCreate' => $this->authorizationChecker->can($currentUserId, 'users.list.create'),
            'canShareTeam' => $presetShareCapabilities->canShareTeam,
            'canShareGlobal' => $presetShareCapabilities->canShareGlobal,
            'shareableTeams' => $presetShareCapabilities->shareableTeams,
        ]);
    }
}
