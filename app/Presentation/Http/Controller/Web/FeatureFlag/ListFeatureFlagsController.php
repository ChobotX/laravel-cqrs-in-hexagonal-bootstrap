<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\FeatureFlag;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;
use Illuminate\View\View;

#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        /** @var list<ResolvedFlag> $flags */
        $flags = $this->queryBus->dispatch(new ListFeatureFlagsQuery);

        $groups = [];
        foreach ($flags as $flag) {
            $groupKey = $flag->definition->group;
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'label' => $flag->definition->groupLabel,
                    'flags' => [],
                ];
            }

            $groups[$groupKey]['flags'][] = $flag;
        }

        return view('feature-flags.index', [
            'groups' => $groups,
            'flagCount' => count($flags),
        ]);
    }
}
