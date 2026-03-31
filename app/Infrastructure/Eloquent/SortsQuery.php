<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use App\Application\Sorting\Sorting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait SortsQuery
{
    /** @return list<string> Columns where case-insensitive sorting via LOWER() applies */
    abstract private function textSortColumns(): array;

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
            if (in_array($sorting->column, $this->textSortColumns(), true)) {
                $builder->orderByRaw(sprintf('LOWER(%s) %s', $sorting->column, $sorting->direction->value));
            } else {
                $builder->orderBy($sorting->column, $sorting->direction->value);
            }
        }

        return $builder;
    }
}
