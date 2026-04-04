<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Repository;

use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\ValueObject\LabelId;
use App\Domain\Label\ValueObject\LabelName;
use App\Domain\Label\ValueObject\LabelNamespace;

interface LabelRepository
{
    public function findById(LabelId $labelId): ?Label;

    public function findByNamespaceAndName(LabelNamespace $labelNamespace, LabelName $labelName): ?Label;

    public function create(Label $label): void;

    public function delete(LabelId $labelId): void;

    /**
     * @param  list<string>  $excludeIds
     * @return list<Label>
     */
    public function search(LabelNamespace $labelNamespace, string $term, array $excludeIds, int $limit): array;

    /** @return list<Label> */
    public function findByLabelableId(string $labelableId): array;

    /**
     * @param  list<string>  $labelableIds
     * @return array<string, list<Label>> labelableId => labels
     */
    public function findByLabelableIds(array $labelableIds): array;

    public function assignLabel(string $labelId, string $labelableId): void;

    public function removeAssignment(string $labelId, string $labelableId): void;

    public function hasAssignments(LabelId $labelId): bool;

    /**
     * @return list<string> orphaned label IDs
     */
    public function removeAllAssignmentsForLabelable(string $labelableId): array;

    /** @param list<string> $labelIds */
    public function deleteByIds(array $labelIds): void;
}
