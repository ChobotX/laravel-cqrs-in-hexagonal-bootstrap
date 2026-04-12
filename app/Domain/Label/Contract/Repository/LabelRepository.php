<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Repository;

use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\ValueObject\LabelId;
use App\Domain\Label\ValueObject\LabelName;
use App\Domain\Label\ValueObject\LabelNamespace;

/**
 * Persistence port for label data in the Label context; implementations live in Infrastructure.
 */
interface LabelRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findById(LabelId $labelId): ?Label;

    /** Loads a record or value object, or null when absent. */
    public function findByNamespaceAndName(LabelNamespace $labelNamespace, LabelName $labelName): ?Label;

    /** Persists a new or updated aggregate row. */
    public function create(Label $label): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(LabelId $labelId): void;

    /**
     * @param  list<string>  $excludeIds
     * @return list<Label>
     *                     Returns a filtered collection according to repository rules.
     */
    public function search(LabelNamespace $labelNamespace, string $term, array $excludeIds, int $limit): array;

    /** @return list<Label> */
    public function findByLabelableId(string $labelableId): array;

    /**
     * @param  list<string>  $labelableIds
     * @return array<string, list<Label>> labelableId => labels
     *                                    Loads a record or value object, or null when absent.
     */
    public function findByLabelableIds(array $labelableIds): array;

    /** Contract operation `assignLabel`; see infrastructure for behavior. */
    public function assignLabel(string $labelId, string $labelableId): void;

    /** Deletes or soft-deletes the targeted record. */
    public function removeAssignment(string $labelId, string $labelableId): void;

    /** Contract operation `hasAssignments`; see infrastructure for behavior. */
    public function hasAssignments(LabelId $labelId): bool;

    /**
     * @return list<string> orphaned label IDs
     *                      Deletes or soft-deletes the targeted record.
     */
    public function removeAllAssignmentsForLabelable(string $labelableId): array;

    /** @param list<string> $labelIds */
    public function deleteByIds(array $labelIds): void;
}
