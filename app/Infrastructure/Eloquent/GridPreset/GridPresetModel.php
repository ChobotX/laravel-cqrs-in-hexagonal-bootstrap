<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\GridPreset;

use App\Infrastructure\Eloquent\HardDelete;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property string $grid_name
 * @property string $name
 * @property string $filters
 * @property string $sorting
 * @property string $search
 * @property bool $is_default
 * @property int $position
 * @property string $scope
 * @property ?string $team_id
 * @property string $created_at
 */
#[HardDelete(reason: 'User-owned UI presets, hard-deleted when user removes them')]
final class GridPresetModel extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'grid_presets';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'grid_name',
        'name',
        'filters',
        'sorting',
        'search',
        'is_default',
        'position',
        'scope',
        'team_id',
    ];

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'position' => 'integer',
        ];
    }
}
