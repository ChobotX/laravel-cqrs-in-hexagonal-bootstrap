<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Repository;

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

interface GridPresetRepository
{
    /**
     * @param  list<string>  $teamIds
     * @return list<GridPreset>
     */
    public function findVisibleByGrid(string $userId, string $gridName, array $teamIds = []): array;

    public function findById(GridPresetId $gridPresetId): ?GridPreset;

    public function save(GridPreset $gridPreset): void;

    public function delete(GridPresetId $gridPresetId): void;

    public function clearDefault(string $userId, string $gridName): void;
}
