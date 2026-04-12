<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\AuditLog;

use App\Domain\AuditLog\Exception\ImmutableAuditLogException;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $trace_id
 * @property string|null $user_id
 * @property string|null $impersonator_id
 * @property string $command
 * @property string $action_label
 * @property string|null $entity_type
 * @property string|null $entity_id
 * @property array<string, mixed> $payload
 * @property list<array<string, mixed>> $changes
 * @property string $status
 * @property string|null $ip_address
 * @property DateTimeImmutable $occurred_at
 */
final class AuditLogModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'audit_log';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'trace_id',
        'user_id',
        'impersonator_id',
        'command',
        'action_label',
        'entity_type',
        'entity_id',
        'payload',
        'changes',
        'status',
        'ip_address',
        'occurred_at',
    ];

    #[Override]
    public function save(array $options = []): bool
    {
        if ($this->exists) {
            throw new ImmutableAuditLogException('updated');
        }

        return parent::save($options);
    }

    #[Override]
    public function delete(): ?bool
    {
        throw new ImmutableAuditLogException('deleted');
    }

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'changes' => 'array',
            'occurred_at' => 'immutable_datetime',
        ];
    }
}
