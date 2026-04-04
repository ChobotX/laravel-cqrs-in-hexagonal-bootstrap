<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
use Illuminate\Database\UniqueConstraintViolationException;

final readonly class EloquentDefinitionRepository implements DefinitionRepository
{
    public function __construct(
        private DefinitionMapper $definitionMapper,
    ) {}

    public function findById(DefinitionId $definitionId): ?Definition
    {
        $model = DefinitionModel::find($definitionId->value);

        if (! $model instanceof DefinitionModel) {
            return null;
        }

        return $this->definitionMapper->toDomain($model);
    }

    public function findByNamespaceAndSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): ?Definition
    {
        $model = DefinitionModel::where('namespace', $definitionNamespace->value)
            ->where('slug', $definitionSlug->value)
            ->first();

        if (! $model instanceof DefinitionModel) {
            return null;
        }

        return $this->definitionMapper->toDomain($model);
    }

    public function create(Definition $definition): void
    {
        try {
            $definitionModel = new DefinitionModel;
            $definitionModel->id = $definition->id->value;
            $definitionModel->namespace = $definition->namespace->value;
            $definitionModel->slug = $definition->slug->value;
            $definitionModel->name = $definition->name->value;
            $definitionModel->save();
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
    public function allPaginated(Pagination $pagination, ?DefinitionNamespace $definitionNamespace = null): PaginatedResult
    {
        $query = DefinitionModel::query();

        if ($definitionNamespace instanceof DefinitionNamespace) {
            $query->where('namespace', $definitionNamespace->value);
        }

        $total = $query->count();

        $models = $query->orderByRaw('LOWER(name) ASC')
            ->offset($pagination->offset())
            ->limit($pagination->perPage)
            ->get();

        $items = array_values(
            $models->map(fn (DefinitionModel $definitionModel): Definition => $this->definitionMapper->toDomain($definitionModel))->all(),
        );

        return new PaginatedResult($items, $total, $pagination);
    }

    /** @return list<string> */
    public function allNamespaces(): array
    {
        /** @var list<string> $namespaces */
        $namespaces = DefinitionModel::query()
            ->distinct()
            ->orderBy('namespace')
            ->pluck('namespace')
            ->all();

        return $namespaces;
    }
}
