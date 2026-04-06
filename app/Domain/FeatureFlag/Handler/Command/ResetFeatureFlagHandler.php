<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\FeatureFlag\Contract\Command\ResetFeatureFlagCommand;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagReset;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagCacheInvalidator;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use DateTimeImmutable;

/** @implements CommandHandler<ResetFeatureFlagCommand> */
final readonly class ResetFeatureFlagHandler implements CommandHandler
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

        $this->featureFlagOverrideRepository->delete(new FlagKey($command->key));

        $this->featureFlagCacheInvalidator->invalidate();

        $this->eventCollector->collect(new FeatureFlagReset(
            key: $command->key,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
