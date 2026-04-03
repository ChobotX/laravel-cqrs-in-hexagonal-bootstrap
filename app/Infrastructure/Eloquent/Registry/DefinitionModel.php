<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $namespace
 * @property string $slug
 * @property string $name
 */
#[HardDelete(reason: 'Definitions are deleted explicitly when no entries exist')]
final class DefinitionModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'definitions';

    protected $fillable = ['id', 'namespace', 'slug', 'name'];

    protected $keyType = 'string';
}
