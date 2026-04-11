<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\FeatureFlag\Constant\FeatureFlagFields;
use App\Domain\FeatureFlag\Contract\Command\UpdateFeatureFlagCommand;
use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagUpdated;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagCacheInvalidator;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;
use App\Domain\FeatureFlag\Service\FlagValueValidator;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateFeatureFlagCommand> */
final readonly class UpdateFeatureFlagHandler implements CommandHandler
{
    public function __construct(
        private FeatureFlagDefinitionProvider $featureFlagDefinitionProvider,
        private FeatureFlagOverrideRepository $featureFlagOverrideRepository,
        private FeatureFlagCacheInvalidator $featureFlagCacheInvalidator,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definition = $this->featureFlagDefinitionProvider->get($command->key);

        if (! $definition instanceof FlagDefinition) {
            throw new FeatureFlagNotFoundException($command->key);
        }

        $existing = $this->featureFlagOverrideRepository->findByKey(new FlagKey($command->key));

        $value = $this->resolveValue($command, $definition, $existing);

        $flagValueValidator = new FlagValueValidator;
        $flagValueValidator->validate($definition, $value);

        $changes = $this->buildChanges($existing, $value, $command->enabled);

        if ($changes === []) {
            return;
        }

        $this->featureFlagOverrideRepository->save(new FeatureFlagOverride(
            key: $command->key,
            value: $value,
            enabled: $command->enabled,
        ));

        $this->featureFlagCacheInvalidator->invalidate();

        $this->eventCollector->collect(new FeatureFlagUpdated(
            key: $command->key,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    private function resolveValue(UpdateFeatureFlagCommand $updateFeatureFlagCommand, FlagDefinition $flagDefinition, ?FeatureFlagOverride $featureFlagOverride): string
    {
        if ($updateFeatureFlagCommand->value !== null) {
            return $updateFeatureFlagCommand->value;
        }

        if ($flagDefinition->type === FlagType::Boolean) {
            return $updateFeatureFlagCommand->enabled ? ResolvedFlag::ENABLED : '0';
        }

        return $featureFlagOverride->value ?? $flagDefinition->default;
    }

    /** @return list<PropertyChange> */
    private function buildChanges(?FeatureFlagOverride $featureFlagOverride, string $value, bool $enabled): array
    {
        $changes = [];

        $oldValue = $featureFlagOverride?->value;
        $oldEnabled = $featureFlagOverride?->enabled;

        if ($oldValue !== $value) {
            $changes[] = new PropertyChange(FeatureFlagFields::VALUE, $oldValue, $value);
        }

        if ($oldEnabled !== $enabled) {
            $changes[] = new PropertyChange(FeatureFlagFields::ENABLED, $oldEnabled, $enabled);
        }

        return $changes;
    }
}
