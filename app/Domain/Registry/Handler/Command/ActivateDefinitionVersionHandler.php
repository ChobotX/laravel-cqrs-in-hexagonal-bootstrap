<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\Command\ActivateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\Event\DefinitionVersionActivated;
use App\Domain\Registry\Contract\Repository\DefinitionVersionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<ActivateDefinitionVersionCommand> */
final readonly class ActivateDefinitionVersionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionVersionRepository $definitionVersionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definitionVersionId = new DefinitionVersionId($command->id);
        $version = $this->definitionVersionRepository->findById($definitionVersionId);

        if (! $version instanceof DefinitionVersion) {
            throw new DefinitionVersionNotFoundException($command->id);
        }

        $this->definitionVersionRepository->deactivateAllForDefinition($version->definitionId);
        $this->definitionVersionRepository->updateStatus($version->id, VersionStatus::Active);

        $this->eventCollector->collect(new DefinitionVersionActivated(
            versionId: $version->id->value,
            definitionId: $version->definitionId->value,
            version: $version->version->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
