<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Repository;

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

/**
 * Persistence port for grid preset data in the GridPreset context; implementations live in Infrastructure.
 */
interface GridPresetRepository
{
    /**
     * @param  list<string>  $teamIds
     * @return list<GridPreset>
     *                          Loads a record or value object, or null when absent.
     */
    public function findVisibleByGrid(string $userId, string $gridName, array $teamIds = []): array;

    /** Loads a record or value object, or null when absent. */
    public function findById(GridPresetId $gridPresetId): ?GridPreset;

    /** Persists a new or updated aggregate row. */
    public function save(GridPreset $gridPreset): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(GridPresetId $gridPresetId): void;

    /** Contract operation `clearDefault`; see infrastructure for behavior. */
    public function clearDefault(string $userId, string $gridName): void;
}
