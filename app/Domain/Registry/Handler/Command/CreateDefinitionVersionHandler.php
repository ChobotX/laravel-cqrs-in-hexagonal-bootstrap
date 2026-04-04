<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\Command\CreateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Event\DefinitionVersionCreated;
use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<CreateDefinitionVersionCommand> */
final readonly class CreateDefinitionVersionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
        private DefinitionVersionRepository $definitionVersionRepository,
        private SchemaSerializer $schemaSerializer,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definitionId = new DefinitionId($command->definitionId);
        $definition = $this->definitionRepository->findById($definitionId);

        if (! $definition instanceof Definition) {
            throw new DefinitionNotFoundException($command->definitionId);
        }

        $versionNumber = $this->definitionVersionRepository->nextVersionNumber($definition->id);
        $schema = $this->schemaSerializer->fromJsonSchema($command->schemaData);

        $definitionVersion = new DefinitionVersion(
            id: new DefinitionVersionId($command->id),
            definitionId: $definition->id,
            version: $versionNumber,
            schema: $schema,
            status: VersionStatus::Draft,
        );

        $this->definitionVersionRepository->create($definitionVersion);

        $this->eventCollector->collect(new DefinitionVersionCreated(
            versionId: $definitionVersion->id->value,
            definitionId: $definition->id->value,
            version: $definitionVersion->version->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
