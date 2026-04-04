<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Label;

use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\Exception\LabelAlreadyExistsException;
use App\Domain\Label\Contract\Repository\LabelRepository;
use App\Domain\Label\Contract\ValueObject\LabelId;
use App\Domain\Label\ValueObject\LabelName;
use App\Domain\Label\ValueObject\LabelNamespace;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

final readonly class EloquentLabelRepository implements LabelRepository
{
    public function __construct(
        private LabelMapper $labelMapper,
    ) {}

    public function findById(LabelId $labelId): ?Label
    {
        $model = LabelModel::find($labelId->value);

        if (! $model instanceof LabelModel) {
            return null;
        }

        return $this->labelMapper->toDomain($model);
    }

    public function findByNamespaceAndName(LabelNamespace $labelNamespace, LabelName $labelName): ?Label
    {
        $model = LabelModel::where('namespace', $labelNamespace->value)
            ->where('name', $labelName->value)
            ->first();

        if (! $model instanceof LabelModel) {
            return null;
        }

        return $this->labelMapper->toDomain($model);
    }

    public function create(Label $label): void
    {
        try {
            $labelModel = new LabelModel;
            $labelModel->id = $label->id->value;
            $labelModel->namespace = $label->namespace->value;
            $labelModel->name = $label->name->value;
            $labelModel->save();
        } catch (UniqueConstraintViolationException) {
            throw new LabelAlreadyExistsException($label->namespace->value, $label->name->value);
        }
    }

    public function delete(LabelId $labelId): void
    {
        LabelModel::where('id', $labelId->value)->delete();
    }

    public function search(LabelNamespace $labelNamespace, string $term, array $excludeIds, int $limit): array
    {
        $builder = LabelModel::where('namespace', $labelNamespace->value);

        if ($term !== '') {
            $builder->whereRaw('unaccent(name) ILIKE unaccent(?)', [sprintf('%%%s%%', $term)]);
        }

        if ($excludeIds !== []) {
            $builder->whereNotIn('id', $excludeIds);
        }

        return array_values(
            $builder->orderByRaw('LOWER(name) ASC')
                ->limit($limit)
                ->get()
                ->map(fn (LabelModel $labelModel): Label => $this->labelMapper->toDomain($labelModel))
                ->all(),
        );
    }

    public function findByLabelableId(string $labelableId): array
    {
        return array_values(
            LabelModel::query()
                ->join('label_assignments', 'labels.id', '=', 'label_assignments.label_id')
                ->where('label_assignments.labelable_id', $labelableId)
                ->orderByRaw('LOWER(labels.name) ASC')
                ->select('labels.*')
                ->get()
                ->map(fn (LabelModel $labelModel): Label => $this->labelMapper->toDomain($labelModel))
                ->all(),
        );
    }

    /** @return array<string, list<Label>> */
    public function findByLabelableIds(array $labelableIds): array
    {
        if ($labelableIds === []) {
            return [];
        }

        /** @var array<string, list<Label>> $map */
        $map = [];

        foreach ($labelableIds as $labelableId) {
            $map[$labelableId] = [];
        }

        $rows = LabelModel::query()
            ->join('label_assignments', 'labels.id', '=', 'label_assignments.label_id')
            ->whereIn('label_assignments.labelable_id', $labelableIds)
            ->orderByRaw('LOWER(labels.name) ASC')
            ->select('labels.*', 'label_assignments.labelable_id')
            ->get();

        foreach ($rows as $row) {
            /** @var string $labelableId */
            $labelableId = $row->getAttribute('labelable_id');
            $map[$labelableId][] = $this->labelMapper->toDomain($row);
        }

        return $map;
    }

    public function assignLabel(string $labelId, string $labelableId): void
    {
        DB::table('label_assignments')->insertOrIgnore([
            'label_id' => $labelId,
            'labelable_id' => $labelableId,
            'created_at' => now(),
        ]);
    }

    public function removeAssignment(string $labelId, string $labelableId): void
    {
        DB::table('label_assignments')
            ->where('label_id', $labelId)
            ->where('labelable_id', $labelableId)
            ->delete();
    }

    public function hasAssignments(LabelId $labelId): bool
    {
        return DB::table('label_assignments')
            ->where('label_id', $labelId->value)
            ->exists();
    }

    public function removeAllAssignmentsForLabelable(string $labelableId): array
    {
        /** @var list<string> $labelIds */
        $labelIds = DB::table('label_assignments')
            ->where('labelable_id', $labelableId)
            ->pluck('label_id')
            ->all();

        DB::table('label_assignments')
            ->where('labelable_id', $labelableId)
            ->delete();

        if ($labelIds === []) {
            return [];
        }

        /** @var list<string> $orphanedIds */
        $orphanedIds = DB::table('labels')
            ->whereIn('id', $labelIds)
            ->whereNotExists(function (\Illuminate\Database\Query\Builder $builder): void {
                $builder->select(DB::raw('1'))
                    ->from('label_assignments')
                    ->whereColumn('label_assignments.label_id', 'labels.id');
            })
            ->pluck('id')
            ->all();

        return $orphanedIds;
    }

    public function deleteByIds(array $labelIds): void
    {
        if ($labelIds === []) {
            return;
        }

        LabelModel::whereIn('id', $labelIds)->delete();
    }
}
