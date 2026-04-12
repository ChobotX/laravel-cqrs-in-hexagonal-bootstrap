<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use App\Application\Authorization\AccessContext;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait ScopesOwnedQuery
{
    /**
     * Apply scope + sharing filter for entities with an owner column.
     *
     * - All scope (or null context): no filter applied.
     * - Own/Team scope: WHERE (owner_column IN (visibleIds) OR id IN (sharedResourceIds)).
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $builder
     * @return Builder<TModel>
     */
    private function applyScopeFilter(
        Builder $builder,
        ?AccessContext $accessContext,
        string $ownerColumn = 'created_by_user_id',
    ): Builder {
        if (! $accessContext instanceof AccessContext || $accessContext->scope === AccessScope::All) {
            return $builder;
        }

        $visibleIds = $accessContext->visibleIds ?? [];
        $sharedIds = $accessContext->sharedResourceIds ?? [];
        $keyName = $builder->getModel()->getKeyName();

        return $builder->where(function (Builder $builder) use ($visibleIds, $sharedIds, $ownerColumn, $keyName): void {
            if ($visibleIds !== []) {
                $builder->whereIn($ownerColumn, $visibleIds);
            }

            if ($sharedIds !== []) {
                $builder->orWhereIn($keyName, $sharedIds);
            }

            if ($visibleIds === [] && $sharedIds === []) {
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
