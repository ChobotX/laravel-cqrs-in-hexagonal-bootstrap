<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Repository\EntryRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
use App\Infrastructure\Eloquent\FiltersQuery;

final readonly class EloquentEntryRepository implements EntryRepository
{
    use FiltersQuery;

    public function __construct(
        private EntryMapper $entryMapper,
    ) {}

    public function findById(EntryId $entryId): ?Entry
    {
        $model = EntryModel::find($entryId->value);

        if (! $model instanceof EntryModel) {
            return null;
        }

        return $this->entryMapper->toDomain($model);
    }

    public function create(Entry $entry): void
    {
        $entryModel = new EntryModel;
        $entryModel->id = $entry->id->value;
        $entryModel->definition_id = $entry->definitionId->value;
        $entryModel->definition_version = $entry->definitionVersion->value;
        $entryModel->namespace = $entry->namespace->value;
        $entryModel->title = $entry->title->value;
        $entryModel->data = $entry->data;
        $entryModel->save();
    }

    public function update(Entry $entry): void
    {
        $model = EntryModel::find($entry->id->value);

        if (! $model instanceof EntryModel) {
            return;
        }

        $model->title = $entry->title->value;
        $model->data = $entry->data;
        $model->save();
    }

    public function delete(EntryId $entryId): void
    {
        EntryModel::where('id', $entryId->value)->delete();
    }

    /** @return PaginatedResult<Entry> */
    public function findByDefinitionPaginated(DefinitionId $definitionId, Pagination $pagination, array $filters = []): PaginatedResult
    {
        $builder = $this->filterBuilder(
            EntryModel::where('definition_id', $definitionId->value),
            $filters,
        );

        $total = $builder->count();

        $models = $builder->orderByRaw('LOWER(title) ASC')
            ->offset($pagination->offset())
            ->limit($pagination->perPage)
            ->get();

        $items = array_values(
            $models->map(fn (EntryModel $entryModel): Entry => $this->entryMapper->toDomain($entryModel))->all(),
        );

        return new PaginatedResult($items, $total, $pagination);
    }

    public function existsByDefinition(DefinitionId $definitionId): bool
    {
        return EntryModel::where('definition_id', $definitionId->value)->exists();
    }

    /** @return list<Entry> */
    public function findByDefinitionSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): array
    {
        return array_values(
            EntryModel::query()
                ->join('definitions', 'entries.definition_id', '=', 'definitions.id')
                ->where('definitions.namespace', $definitionNamespace->value)
                ->where('definitions.slug', $definitionSlug->value)
                ->orderByRaw('LOWER(entries.title) ASC')
                ->select('entries.*')
                ->get()
                ->map(fn (EntryModel $entryModel): Entry => $this->entryMapper->toDomain($entryModel))
                ->all(),
        );
    }

    /** @return list<string> */
    private function searchColumns(): array
    {
        return ['title'];
    }
}
