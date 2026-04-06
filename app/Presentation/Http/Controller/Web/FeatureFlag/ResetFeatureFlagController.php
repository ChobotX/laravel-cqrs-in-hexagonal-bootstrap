<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\FeatureFlag;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\FeatureFlag\Contract\Command\ResetFeatureFlagCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('feature_flags.management.update')]
final readonly class ResetFeatureFlagController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $key): RedirectResponse
    {
        $this->commandBus->dispatch(new ResetFeatureFlagCommand(key: $key));

        return redirect()->route('feature-flags.index')
            ->with('success', __('messages.feature_flags.reset'));
    }
}
