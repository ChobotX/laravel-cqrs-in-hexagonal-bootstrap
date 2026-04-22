<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\FeatureFlag;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\FeatureFlag\Contract\Command\UpdateFeatureFlagCommand;
use App\Presentation\Http\Request\Web\FeatureFlag\UpdateFeatureFlagRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('feature_flags.management.update')]
final readonly class UpdateFeatureFlagController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateFeatureFlagRequest $updateFeatureFlagRequest, string $key): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateFeatureFlagCommand(
            key: $key,
            enabled: $updateFeatureFlagRequest->flagEnabled(),
            value: $updateFeatureFlagRequest->flagValue(),
        ));

        return redirect()->route('feature-flags.index')
            ->with('success', __('messages.feature_flags.updated'));
    }
}
