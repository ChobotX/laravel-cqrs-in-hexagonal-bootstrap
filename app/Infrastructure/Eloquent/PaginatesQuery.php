<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use App\Application\Pagination\Pagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait PaginatesQuery
{
    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $builder
     * @return array{list<TModel>, int}
     */
    private function paginateBuilder(Builder $builder, Pagination $pagination): array
    {
        $total = $builder->count();

        /** @var list<TModel> $models */
        $models = array_values(
            $builder
                ->offset($pagination->offset())
                ->limit($pagination->perPage)
                ->get()
                ->all(),
        );

        return [$models, $total];
    }
}
