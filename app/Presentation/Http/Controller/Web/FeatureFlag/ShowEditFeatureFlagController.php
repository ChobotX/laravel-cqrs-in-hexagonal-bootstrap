<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\FeatureFlag;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\FeatureFlag\Contract\Query\GetFeatureFlagQuery;
use Illuminate\View\View;

#[RequiresPermission('feature_flags.management.read')]
final readonly class ShowEditFeatureFlagController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $key): View
    {
        $resolvedFlag = $this->queryBus->dispatch(new GetFeatureFlagQuery($key));

        return view('feature-flags.edit', [
            'flag' => $resolvedFlag,
        ]);
    }
}
