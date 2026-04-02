<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\File;

use App\Infrastructure\Eloquent\HasOptimisticLocking;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * @property string $id
 * @property string $namespace
 * @property string $original_name
 * @property string $storage_path
 * @property string $mime_type
 * @property int $size_in_bytes
 * @property int $version_number
 * @property string $uploaded_by
 * @property DateTimeImmutable $uploaded_at
 */
final class FileModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'files';

    protected $fillable = ['id', 'namespace', 'original_name', 'storage_path', 'mime_type', 'size_in_bytes', 'version_number', 'uploaded_by', 'uploaded_at'];

    protected $keyType = 'string';

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'uploaded_at' => 'immutable_datetime',
            'size_in_bytes' => 'integer',
            'version_number' => 'integer',
        ];
    }
}
