<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use App\Application\Sorting\Sorting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait SortsQuery
{
    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $builder
     * @param  list<Sorting>  $sortings
     * @return Builder<TModel>
     */
    private function sortBuilder(Builder $builder, array $sortings): Builder
    {
        foreach ($sortings as $sorting) {
            $builder->orderByRaw(sprintf('LOWER(%s) %s', $sorting->column, $sorting->direction->value));
        }

        return $builder;
    }
}
