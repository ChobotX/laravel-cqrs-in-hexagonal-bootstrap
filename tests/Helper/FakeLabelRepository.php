<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Label\Label;
use App\Domain\Label\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use App\Domain\Label\LabelRepository;

final class FakeLabelRepository implements LabelRepository
{
    /** @var list<Label> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /**
     * @param  array<string, Label>  $labels
     * @param  list<array{labelId: string, labelableId: string}>  $assignments
     */
    public function __construct(
        private array $labels = [],
        private array $assignments = [],
    ) {}

    public function findById(LabelId $labelId): ?Label
    {
        return $this->labels[$labelId->value] ?? null;
    }

    public function findByNamespaceAndName(LabelNamespace $labelNamespace, LabelName $labelName): ?Label
    {
        foreach ($this->labels as $label) {
            if ($label->namespace->value === $labelNamespace->value && $label->name->value === $labelName->value) {
                return $label;
            }
        }

        return null;
    }

    public function create(Label $label): void
    {
        $this->saved[] = $label;
        $this->labels[$label->id->value] = $label;
    }

    public function delete(LabelId $labelId): void
    {
        $this->deleted[] = $labelId->value;
        unset($this->labels[$labelId->value]);
    }

    /** @return list<Label> */
    public function search(LabelNamespace $labelNamespace, string $term, array $excludeIds, int $limit): array
    {
        $normalizedTerm = mb_strtolower($term);

        $results = array_filter(
            $this->labels,
            function (Label $label) use ($labelNamespace, $normalizedTerm, $excludeIds): bool {
                if ($label->namespace->value !== $labelNamespace->value) {
                    return false;
                }

                if (in_array($label->id->value, $excludeIds, true)) {
                    return false;
                }

                return str_contains(mb_strtolower($label->name->value), $normalizedTerm);
            },
        );

        return array_values(array_slice($results, 0, $limit));
    }

    /** @return list<Label> */
    public function findByLabelableId(string $labelableId): array
    {
        $labelIds = array_map(
            fn (array $a): string => $a['labelId'],
            array_filter(
                $this->assignments,
                fn (array $a): bool => $a['labelableId'] === $labelableId,
            ),
        );

        return array_values(array_filter(
            $this->labels,
            fn (Label $label): bool => in_array($label->id->value, $labelIds, true),
        ));
    }

    public function assignLabel(string $labelId, string $labelableId): void
    {
        $this->assignments[] = ['labelId' => $labelId, 'labelableId' => $labelableId];
    }

    public function removeAssignment(string $labelId, string $labelableId): void
    {
        $this->assignments = array_values(array_filter(
            $this->assignments,
            fn (array $a): bool => ! ($a['labelId'] === $labelId && $a['labelableId'] === $labelableId),
        ));
    }

    public function hasAssignments(LabelId $labelId): bool
    {
        return array_any($this->assignments, fn ($assignment): bool => $assignment['labelId'] === $labelId->value);
    }

    /** @return list<string> */
    public function removeAllAssignmentsForLabelable(string $labelableId): array
    {
        $affectedLabelIds = array_unique(array_map(
            fn (array $a): string => $a['labelId'],
            array_filter(
                $this->assignments,
                fn (array $a): bool => $a['labelableId'] === $labelableId,
            ),
        ));

        $this->assignments = array_values(array_filter(
            $this->assignments,
            fn (array $a): bool => $a['labelableId'] !== $labelableId,
        ));

        $orphaned = [];
        foreach ($affectedLabelIds as $affectedLabelId) {
            if (! $this->hasAssignments(new LabelId($affectedLabelId))) {
                $orphaned[] = $affectedLabelId;
            }
        }

        return $orphaned;
    }

    /** @param list<string> $labelIds */
    public function deleteByIds(array $labelIds): void
    {
        foreach ($labelIds as $labelId) {
            $this->deleted[] = $labelId;
            unset($this->labels[$labelId]);
        }
    }
}
