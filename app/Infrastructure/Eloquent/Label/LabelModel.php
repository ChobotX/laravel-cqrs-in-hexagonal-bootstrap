<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Label;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class LabelModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $table = 'labels';

    protected $fillable = ['id', 'namespace', 'name'];

    protected $keyType = 'string';
}
