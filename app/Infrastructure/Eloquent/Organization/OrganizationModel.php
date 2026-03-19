<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use Database\Factories\OrganizationModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** @use HasFactory<OrganizationModelFactory> */
final class OrganizationModel extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'organizations';

    protected $fillable = ['id', 'name', 'slug', 'description'];

    protected $keyType = 'string';

    protected static function newFactory(): OrganizationModelFactory
    {
        return OrganizationModelFactory::new();
    }
}
