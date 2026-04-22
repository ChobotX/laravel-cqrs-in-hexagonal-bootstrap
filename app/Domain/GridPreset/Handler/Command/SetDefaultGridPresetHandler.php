<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\GridPreset\Contract\Command\SetDefaultGridPresetCommand;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\GridPresetNotFoundException;
use App\Domain\GridPreset\Exception\GridPresetOwnershipException;

/** @implements CommandHandler<SetDefaultGridPresetCommand> */
#[SkipDomainEvent(reason: 'UI convenience — grid preset default toggle produces no business event')]
final readonly class SetDefaultGridPresetHandler implements CommandHandler
{
    public function __construct(
        private GridPresetRepository $gridPresetRepository,
    ) {}

    public function handle(Command $command): void
    {
        $gridPresetId = new GridPresetId($command->id);
        $preset = $this->gridPresetRepository->findById($gridPresetId);

        if (! $preset instanceof GridPreset) {
            throw new GridPresetNotFoundException($command->id);
        }

        if ($preset->userId !== $command->userId) {
            throw new GridPresetOwnershipException($command->id);
        }

        $this->gridPresetRepository->clearDefault($command->userId, $command->gridName);

        $this->gridPresetRepository->save(new GridPreset(
            $preset->id,
            $preset->userId,
            $preset->gridName,
            $preset->name,
            $preset->filters,
            $preset->sorting,
            $preset->search,
            true,
            $preset->position,
            $preset->scope,
            $preset->teamId,
        ));
    }
}
