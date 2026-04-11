<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Constant\DefinitionFields;
use App\Domain\Registry\Contract\Command\UpdateDefinitionCommand;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Event\DefinitionUpdated;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\ValueObject\DefinitionName;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateDefinitionCommand> */
final readonly class UpdateDefinitionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definitionId = new DefinitionId($command->id);
        $existing = $this->definitionRepository->findById($definitionId);

        if (! $existing instanceof Definition) {
            throw new DefinitionNotFoundException($command->id);
        }

        $definitionName = new DefinitionName($command->name);

        $changes = $this->buildChanges($existing, $definitionName);

        if ($changes === []) {
            return;
        }

        $definition = new Definition(
            id: $existing->id,
            namespace: $existing->namespace,
            slug: $existing->slug,
            name: $definitionName,
        );

        $this->definitionRepository->update($definition);

        $this->eventCollector->collect(new DefinitionUpdated(
            definitionId: $existing->id->value,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    /** @return list<PropertyChange> */
    private function buildChanges(Definition $definition, DefinitionName $definitionName): array
    {
        $changes = [];

        if ($definition->name->value !== $definitionName->value) {
            $changes[] = new PropertyChange(DefinitionFields::NAME, $definition->name->value, $definitionName->value);
        }

        return $changes;
    }
}
