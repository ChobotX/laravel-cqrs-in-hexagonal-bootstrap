<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\FeatureFlag;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\FeatureFlag\Contract\Command\UpdateFeatureFlagCommand;
use App\Presentation\Http\Request\InternalApi\FeatureFlag\ToggleFeatureFlagRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('feature_flags.management.update')]
final readonly class ToggleFeatureFlagController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ToggleFeatureFlagRequest $toggleFeatureFlagRequest, string $key): JsonResponse
    {
        $this->commandBus->dispatch(new UpdateFeatureFlagCommand(
            key: $key,
            enabled: $toggleFeatureFlagRequest->flagEnabled(),
        ));

        return new JsonResponse(['success' => true]);
    }
}
