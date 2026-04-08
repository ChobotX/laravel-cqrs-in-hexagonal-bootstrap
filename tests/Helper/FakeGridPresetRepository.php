<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

final class FakeGridPresetRepository implements GridPresetRepository
{
    /** @var list<GridPreset> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, GridPreset> $presets */
    public function __construct(
        private array $presets = [],
    ) {}

    /** @return list<GridPreset> */
    public function findVisibleByGrid(string $userId, string $gridName, array $teamIds = []): array
    {
        return array_values(array_filter(
            $this->presets,
            fn (GridPreset $gridPreset): bool => $gridPreset->gridName === $gridName
                && (
                    ($gridPreset->userId === $userId && $gridPreset->scope === PresetScope::Personal)
                    || $gridPreset->scope === PresetScope::Global
                    || ($gridPreset->scope === PresetScope::Team && $gridPreset->teamId !== null && in_array($gridPreset->teamId, $teamIds, true))
                ),
        ));
    }

    public function findById(GridPresetId $gridPresetId): ?GridPreset
    {
        return $this->presets[$gridPresetId->value] ?? null;
    }

    public function save(GridPreset $gridPreset): void
    {
        $this->saved[] = $gridPreset;
        $this->presets[$gridPreset->id->value] = $gridPreset;
    }

    public function delete(GridPresetId $gridPresetId): void
    {
        $this->deleted[] = $gridPresetId->value;
        unset($this->presets[$gridPresetId->value]);
    }

    public function clearDefault(string $userId, string $gridName): void
    {
        foreach ($this->presets as $key => $preset) {
            if ($preset->userId === $userId && $preset->gridName === $gridName && $preset->isDefault) {
                $this->presets[$key] = new GridPreset(
                    $preset->id,
                    $preset->userId,
                    $preset->gridName,
                    $preset->name,
                    $preset->filters,
                    $preset->sorting,
                    $preset->search,
                    false,
                    $preset->position,
                    $preset->scope,
                    $preset->teamId,
                );
            }
        }
    }
}
