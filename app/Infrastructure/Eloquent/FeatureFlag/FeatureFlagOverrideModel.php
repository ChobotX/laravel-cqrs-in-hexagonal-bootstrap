<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\FeatureFlag;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Model;
use Override;

#[HardDelete(reason: 'Feature flag overrides are reset by deletion, returning to config default')]
final class FeatureFlagOverrideModel extends Model
{
    use HasOptimisticLocking;

    public $incrementing = false;

    protected $table = 'feature_flag_overrides';

    protected $primaryKey = 'key';

    protected $fillable = ['key', 'enabled', 'value'];

    protected $keyType = 'string';

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
