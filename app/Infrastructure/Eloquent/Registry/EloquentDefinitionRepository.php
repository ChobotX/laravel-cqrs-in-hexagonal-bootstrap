<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use Illuminate\Database\UniqueConstraintViolationException;

final readonly class EloquentDefinitionRepository implements DefinitionRepository
{
    public function __construct(
        private DefinitionMapper $mapper,
    ) {}

    public function findById(DefinitionId $definitionId): ?Definition
    {
        $model = DefinitionModel::find($definitionId->value);

        if (! $model instanceof DefinitionModel) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findByNamespaceAndSlug(DefinitionNamespace $namespace, DefinitionSlug $slug): ?Definition
    {
        $model = DefinitionModel::where('namespace', $namespace->value)
            ->where('slug', $slug->value)
            ->first();

        if (! $model instanceof DefinitionModel) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function create(Definition $definition): void
    {
        try {
            $model = new DefinitionModel;
            $model->id = $definition->id->value;
            $model->namespace = $definition->namespace->value;
            $model->slug = $definition->slug->value;
            $model->name = $definition->name->value;
            $model->save();
        } catch (UniqueConstraintViolationException) {
            throw new DefinitionAlreadyExistsException($definition->namespace->value, $definition->slug->value);
        }
    }

    public function update(Definition $definition): void
    {
        DefinitionModel::where('id', $definition->id->value)
            ->update(['name' => $definition->name->value]);
    }

    public function delete(DefinitionId $definitionId): void
    {
        DefinitionModel::where('id', $definitionId->value)->delete();
    }

    /** @return PaginatedResult<Definition> */
    public function allPaginated(Pagination $pagination, ?DefinitionNamespace $namespace = null): PaginatedResult
    {
        $query = DefinitionModel::query();

        if ($namespace instanceof DefinitionNamespace) {
            $query->where('namespace', $namespace->value);
        }

        $total = $query->count();

        $models = $query->orderByRaw('LOWER(name) ASC')
            ->offset($pagination->offset())
            ->limit($pagination->perPage)
            ->get();

        $items = array_values(
            $models->map(fn (DefinitionModel $m): Definition => $this->mapper->toDomain($m))->all(),
        );

        return new PaginatedResult($items, $total, $pagination);
    }
}
