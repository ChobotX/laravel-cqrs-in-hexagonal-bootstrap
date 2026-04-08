<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\GridPreset\Contract\Command\DeleteGridPresetCommand;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\GridPresetNotFoundException;
use App\Domain\GridPreset\Exception\GridPresetOwnershipException;
use App\Domain\Team\Contract\Service\TeamMembershipChecker;

/** @implements CommandHandler<DeleteGridPresetCommand> */
#[SkipDomainEvent(reason: 'UI convenience — grid preset delete produces no business event')]
final readonly class DeleteGridPresetHandler implements CommandHandler
{
    private const string MANAGEMENT_PERMISSION = 'teams.management.update';

    public function __construct(
        private GridPresetRepository $gridPresetRepository,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    public function handle(Command $command): void
    {
        $gridPresetId = new GridPresetId($command->id);
        $preset = $this->gridPresetRepository->findById($gridPresetId);

        if (! $preset instanceof GridPreset) {
            throw new GridPresetNotFoundException($command->id);
        }

        if ($preset->userId !== $command->userId && ! $this->canDeleteShared($command->userId, $preset)) {
            throw new GridPresetOwnershipException($command->id);
        }

        $this->gridPresetRepository->delete($gridPresetId);
    }

    private function canDeleteShared(string $userId, GridPreset $gridPreset): bool
    {
        return match ($gridPreset->scope) {
            PresetScope::Team => $this->canManageTeamPreset($userId, $gridPreset),
            PresetScope::Global => $this->canManageGlobally($userId),
            PresetScope::Personal => false,
        };
    }

    private function canManageGlobally(string $userId): bool
    {
        $accessDecision = $this->authorizationChecker->canWithScope($userId, self::MANAGEMENT_PERMISSION);

        return $accessDecision->granted() && $accessDecision->scope() === AccessScope::All->value;
    }

    private function canManageTeamPreset(string $userId, GridPreset $gridPreset): bool
    {
        if ($gridPreset->teamId === null) {
            return false;
        }

        $accessDecision = $this->authorizationChecker->canWithScope($userId, self::MANAGEMENT_PERMISSION);

        if (! $accessDecision->granted()) {
            return false;
        }

        if ($accessDecision->scope() === AccessScope::All->value) {
            return true;
        }

        return $accessDecision->scope() === AccessScope::Team->value
            && in_array($gridPreset->teamId, $this->teamMembershipChecker->memberTeamIds($userId), true);
    }
}
