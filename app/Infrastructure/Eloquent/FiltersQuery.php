<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait FiltersQuery
{
    /** @return list<string> Columns for fulltext Search operator */
    abstract private function searchColumns(): array;

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $builder
     * @param  list<Filter>  $filters
     * @return Builder<TModel>
     */
    private function filterBuilder(Builder $builder, array $filters): Builder
    {
        foreach ($filters as $filter) {
            if ($filter->column === '' && $filter->operator !== FilterOperator::Search) {
                continue;
            }

            /** @var string $stringValue */
            $stringValue = $filter->value;

            match ($filter->operator) {
                FilterOperator::Equals => $builder->where($filter->column, $stringValue),
                FilterOperator::Contains => $builder->whereRaw(
                    sprintf('unaccent(%s) ILIKE unaccent(?)', $filter->column),
                    ['%'.$this->escapeLike($stringValue).'%'],
                ),
                FilterOperator::In => is_array($filter->value) && $filter->value !== []
                    ? $builder->whereIn($filter->column, $filter->value)
                    : $builder,
                FilterOperator::Search => $this->applySearch($builder, $stringValue),
            };
        }

        return $builder;
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $builder
     * @return Builder<TModel>
     */
    private function applySearch(Builder $builder, string $term): Builder
    {
        if ($term === '' || $this->searchColumns() === []) {
            return $builder;
        }

        $columns = $this->searchColumns();

        $builder->where(function (Builder $builder) use ($term, $columns): void {
            $escaped = '%'.$this->escapeLike($term).'%';

            foreach ($columns as $column) {
                $builder->orWhereRaw(sprintf('unaccent(%s) ILIKE unaccent(?)', $column), [$escaped]);
            }
        });

        return $builder;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
