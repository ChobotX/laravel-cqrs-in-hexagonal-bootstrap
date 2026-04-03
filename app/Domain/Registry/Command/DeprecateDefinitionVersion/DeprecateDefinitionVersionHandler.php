<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\DeprecateDefinitionVersion;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Event\DefinitionVersionDeprecated;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use App\Domain\Registry\VersionStatus;
use DateTimeImmutable;

/** @implements CommandHandler<DeprecateDefinitionVersionCommand> */
final readonly class DeprecateDefinitionVersionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionVersionRepository $definitionVersionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $versionId = new DefinitionVersionId($command->id);
        $version = $this->definitionVersionRepository->findById($versionId);

        if (! $version instanceof DefinitionVersion) {
            throw new DefinitionVersionNotFoundException($command->id);
        }

        $this->definitionVersionRepository->updateStatus($version->id, VersionStatus::Deprecated);

        $this->eventCollector->collect(new DefinitionVersionDeprecated(
            versionId: $version->id->value,
            definitionId: $version->definitionId->value,
            version: $version->version->value,
            occurredAt: new DateTimeImmutable(),
        ));
    }
}
