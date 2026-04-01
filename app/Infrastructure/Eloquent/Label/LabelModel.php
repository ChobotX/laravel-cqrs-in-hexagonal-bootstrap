<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Label;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[HardDelete(reason: 'Labels are ephemeral assignments, orphan cleanup deletes unused labels')]
final class LabelModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'labels';

    protected $fillable = ['id', 'namespace', 'name'];

    protected $keyType = 'string';
}
