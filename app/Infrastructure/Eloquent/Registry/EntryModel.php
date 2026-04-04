<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $definition_id
 * @property int $definition_version
 * @property string $namespace
 * @property string $title
 * @property array<string, mixed> $data
 */
#[HardDelete(reason: 'Entries are deleted explicitly')]
final class EntryModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'entries';

    protected $fillable = ['id', 'definition_id', 'definition_version', 'namespace', 'title', 'data'];

    protected $keyType = 'string';

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
