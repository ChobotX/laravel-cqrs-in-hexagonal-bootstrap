<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\VersionStatus;
use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $definition_id
 * @property int $version
 * @property array<string, mixed> $body
 * @property VersionStatus $status
 */
#[HardDelete(reason: 'Definition versions are managed through the versioning lifecycle')]
final class DefinitionVersionModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'definition_versions';

    protected $fillable = ['id', 'definition_id', 'version', 'body', 'status'];

    protected $keyType = 'string';

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'body' => 'array',
            'status' => VersionStatus::class,
        ];
    }
}
