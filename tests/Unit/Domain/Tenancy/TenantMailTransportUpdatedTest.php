<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Domain\Tenancy\Contract\Event\TenantMailTransportUpdated;

it('exposes audit metadata and changes', function (): void {
    $changes = [new PropertyChange(property: 'host', old: 'a', new: 'b')];
    $occurredAt = new DateTimeImmutable('2026-04-28T12:00:00+00:00');

    $event = new TenantMailTransportUpdated(
        tenantId: 'tenant-1',
        changes: $changes,
        occurredAt: $occurredAt,
    );

    expect($event->entityType())->toBe('tenant_mail_transport')
        ->and($event->entityId())->toBe('tenant-1')
        ->and($event->changes())->toBe($changes)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->actionLabel())->toBe('Tenant Mail Transport Updated');
});
