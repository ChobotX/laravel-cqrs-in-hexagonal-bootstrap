<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\EmailTemplate;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $type
 * @property string $locale
 * @property string $subject_template
 * @property string $body_template
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[HardDelete(reason: 'Templates are system-seeded data, delete+reseed is the recovery path')]
final class EmailTemplateModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'email_templates';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'locale',
        'subject_template',
        'body_template',
    ];
}
