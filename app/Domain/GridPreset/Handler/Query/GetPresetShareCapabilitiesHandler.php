<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Handler\Query;

use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Bus\QueryBus;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\GridPreset\Contract\ValueObject\PresetShareCapabilities;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\GetUserTeamsQuery;

/** @implements QueryHandler<GetPresetShareCapabilitiesQuery, PresetShareCapabilities> */
final readonly class GetPresetShareCapabilitiesHandler implements QueryHandler
{
    private const string MANAGEMENT_PERMISSION = 'teams.management.update';

    public function __construct(
        private AuthorizationChecker $authorizationChecker,
        private QueryBus $queryBus,
    ) {}

    public function handle(Query $query): PresetShareCapabilities
    {
        $accessDecision = $this->authorizationChecker->canWithScope($query->userId, self::MANAGEMENT_PERMISSION);

        if (! $accessDecision->granted()) {
            return new PresetShareCapabilities(false, false, []);
        }

        $canShareGlobal = $accessDecision->scope() === AccessScope::All->value;
        $canShareTeam = in_array($accessDecision->scope(), [AccessScope::All->value, AccessScope::Team->value], true);

        $shareableTeams = [];

        if ($canShareTeam) {
            /** @var list<Team> $teams */
            $teams = $this->queryBus->dispatch(new GetUserTeamsQuery(userId: $query->userId));

            $shareableTeams = array_map(
                fn (Team $team): array => ['id' => $team->id->value, 'name' => $team->name->value],
                $teams,
            );
        }

        return new PresetShareCapabilities($canShareTeam, $canShareGlobal, $shareableTeams);
    }
}
