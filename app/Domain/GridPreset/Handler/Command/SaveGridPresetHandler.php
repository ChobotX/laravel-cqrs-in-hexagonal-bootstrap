<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\GridPreset\Contract\Command\SaveGridPresetCommand;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\GridPresetScopePermissionException;
use App\Domain\Team\Contract\Service\TeamMembershipChecker;

/** @implements CommandHandler<SaveGridPresetCommand> */
#[SkipDomainEvent(reason: 'UI convenience — grid preset save produces no business event')]
final readonly class SaveGridPresetHandler implements CommandHandler
{
    private const string MANAGEMENT_PERMISSION = 'teams.management.update';

    public function __construct(
        private GridPresetRepository $gridPresetRepository,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    public function handle(Command $command): void
    {
        $presetScope = PresetScope::from($command->scope);

        $this->assertScopePermission($command->userId, $presetScope, $command->teamId);

        $this->gridPresetRepository->save(new GridPreset(
            new GridPresetId($command->id),
            $command->userId,
            $command->gridName,
            $command->name,
            $command->filters,
            $command->sorting,
            $command->search,
            $command->isDefault,
            $command->position,
            $presetScope,
            $command->teamId,
        ));
    }

    private function assertScopePermission(string $userId, PresetScope $presetScope, ?string $teamId): void
    {
        match ($presetScope) {
            PresetScope::Personal => null,
            PresetScope::Team => $this->assertTeamPermission($userId, $teamId),
            PresetScope::Global => $this->assertGlobalPermission($userId),
        };
    }

    private function assertGlobalPermission(string $userId): void
    {
        $accessDecision = $this->authorizationChecker->canWithScope($userId, self::MANAGEMENT_PERMISSION);

        if (! $accessDecision->granted() || $accessDecision->scope() !== AccessScope::All->value) {
            throw new GridPresetScopePermissionException(PresetScope::Global->value);
        }
    }

    private function assertTeamPermission(string $userId, ?string $teamId): void
    {
        if ($teamId === null) {
            throw new GridPresetScopePermissionException(PresetScope::Team->value);
        }

        $accessDecision = $this->authorizationChecker->canWithScope($userId, self::MANAGEMENT_PERMISSION);

        if (! $accessDecision->granted()) {
            throw new GridPresetScopePermissionException(PresetScope::Team->value);
        }

        if ($accessDecision->scope() === AccessScope::All->value) {
            return;
        }

        if ($accessDecision->scope() !== AccessScope::Team->value
            || ! in_array($teamId, $this->teamMembershipChecker->memberTeamIds($userId), true)) {
            throw new GridPresetScopePermissionException(PresetScope::Team->value);
        }
    }
}
