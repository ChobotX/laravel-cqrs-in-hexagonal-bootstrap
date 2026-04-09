<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\FeatureFlag;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\View\View;

#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(): View
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        /** @var list<ResolvedFlag> $flags */
        $flags = $this->queryBus->dispatch(new ListFeatureFlagsQuery);

        $groups = [];
        foreach ($flags as $flag) {
            $groupKey = $flag->definition->group;
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'key' => $groupKey,
                    'label' => $flag->definition->groupLabel,
                ];
            }
        }

        $presetShareCapabilities = $this->queryBus->dispatch(new GetPresetShareCapabilitiesQuery($currentUserId));

        return view('feature-flags.index', [
            'canUpdate' => $this->authorizationChecker->can($currentUserId, 'feature_flags.management.update'),
            'canShareTeam' => $presetShareCapabilities->canShareTeam,
            'canShareGlobal' => $presetShareCapabilities->canShareGlobal,
            'shareableTeams' => $presetShareCapabilities->shareableTeams,
            'groups' => array_values($groups),
        ]);
    }
}
