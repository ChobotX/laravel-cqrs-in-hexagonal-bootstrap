<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\EmailTemplate;

use App\Infrastructure\Eloquent\HardDelete;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property string $id
 * @property string $template_type
 * @property string $locale
 * @property string $recipient_id
 * @property string $recipient_email
 * @property string $rendered_subject
 * @property string $rendered_body_masked
 * @property list<string> $variable_keys
 * @property string|null $trace_id
 * @property Carbon $sent_at
 */
#[HardDelete(reason: 'Operational log data, no soft delete needed')]
final class EmailLogModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'email_logs';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'template_type',
        'locale',
        'recipient_id',
        'recipient_email',
        'rendered_subject',
        'rendered_body_masked',
        'variable_keys',
        'trace_id',
        'sent_at',
    ];

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'variable_keys' => 'array',
            'sent_at' => 'datetime',
        ];
    }
}
