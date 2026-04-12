<?php

declare(strict_types=1);

use App\Application\Bus\Middleware\ProjectAuditLog;
use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Tests\Helper\FakeAuditLogWriter;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeIdGenerator;

it('writes no entry when the handler collected no events', function (): void {
    $writer = new FakeAuditLogWriter;
    $collector = new FakeEventCollector;
    $projectAuditLog = buildProjector($writer, $collector);

    $projectAuditLog->handle(new stdClass, fn (): null => null);

    expect($writer->recorded)->toBeEmpty();
});

it('writes one entry per collected domain event', function (): void {
    $writer = new FakeAuditLogWriter;
    $collector = new FakeEventCollector;
    $collector->collect(new TestPlainEvent('entity-1'));
    $collector->collect(new TestPlainEvent('entity-2'));

    $projectAuditLog = buildProjector($writer, $collector);
    $projectAuditLog->handle(new stdClass, fn (): null => null);

    expect($writer->recorded)->toHaveCount(2)
        ->and($writer->recorded[0]->entityId)->toBe('entity-1')
        ->and($writer->recorded[0]->changes)->toBe([])
        ->and($writer->recorded[0]->status)->toBe(AuditLogStatus::Success)
        ->and($writer->recorded[0]->actionLabel)->toBe('Test Plain Event')
        ->and($writer->recorded[1]->entityId)->toBe('entity-2');
});

it('serializes PropertyChange list for EntityUpdated events', function (): void {
    $writer = new FakeAuditLogWriter;
    $collector = new FakeEventCollector;
    $collector->collect(new TestEntityUpdatedEvent('entity-1', [
        new PropertyChange(property: 'name', old: 'old', new: 'new'),
        PropertyChange::redacted('password'),
    ]));

    $projectAuditLog = buildProjector($writer, $collector);
    $projectAuditLog->handle(new stdClass, fn (): null => null);

    expect($writer->recorded[0]->changes)->toBe([
        ['property' => 'name', 'old' => 'old', 'new' => 'new', 'sensitive' => false],
        ['property' => 'password', 'old' => null, 'new' => null, 'sensitive' => true],
    ]);
});

it('does not write entries when the handler throws', function (): void {
    $writer = new FakeAuditLogWriter;
    $collector = new FakeEventCollector;
    $collector->collect(new TestPlainEvent('entity-1'));

    $projectAuditLog = buildProjector($writer, $collector);

    try {
        $projectAuditLog->handle(new stdClass, fn (): never => throw new RuntimeException('fail'));
    } catch (RuntimeException) {
    }

    expect($writer->recorded)->toBeEmpty();
});

it('propagates actor and trace identifiers onto every entry', function (): void {
    $writer = new FakeAuditLogWriter;
    $collector = new FakeEventCollector;
    $collector->collect(new TestPlainEvent('a'), new TestPlainEvent('b'));

    $projectAuditLog = buildProjector($writer, $collector, userId: 'user-42', traceId: 'trace-xyz');
    $projectAuditLog->handle(new stdClass, fn (): null => null);

    expect($writer->recorded[0]->userId)->toBe('user-42')
        ->and($writer->recorded[0]->traceId)->toBe('trace-xyz')
        ->and($writer->recorded[1]->userId)->toBe('user-42')
        ->and($writer->recorded[1]->traceId)->toBe('trace-xyz');
});

function buildProjector(
    FakeAuditLogWriter $fakeAuditLogWriter,
    FakeEventCollector $fakeEventCollector,
    ?string $userId = 'user-1',
    string $traceId = 'trace-test',
): ProjectAuditLog {
    $authenticatedUser = new readonly class($userId) implements AuthenticatedUser
    {
        public function __construct(private ?string $userId) {}

        public function id(): ?string
        {
            return $this->userId;
        }

        public function name(): ?string
        {
            return null;
        }

        public function impersonatorId(): ?string
        {
            return null;
        }

        public function isImpersonating(): bool
        {
            return false;
        }
    };

    $traceContext = new readonly class($traceId) implements App\Contract\Tracing\TraceContext
    {
        public function __construct(private string $traceId) {}

        public function traceId(): string
        {
            return $this->traceId;
        }

        public function userId(): ?string
        {
            return null;
        }

        public function tenantId(): ?string
        {
            return null;
        }
    };

    return new ProjectAuditLog(
        eventCollector: $fakeEventCollector,
        auditLogWriter: $fakeAuditLogWriter,
        authenticatedUser: $authenticatedUser,
        idGenerator: new FakeIdGenerator,
        traceContext: $traceContext,
    );
}

final readonly class TestPlainEvent implements DomainEvent
{
    public function __construct(public string $id) {}

    public function occurredAt(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-01-01T00:00:00+00:00');
    }

    public function entityType(): string
    {
        return 'test';
    }

    public function entityId(): string
    {
        return $this->id;
    }

    public function actionLabel(): string
    {
        return 'Test Plain Event';
    }
}

final readonly class TestEntityUpdatedEvent implements DomainEvent, EntityUpdated
{
    /** @param list<PropertyChange> $changes */
    public function __construct(
        public string $id,
        public array $changes,
    ) {}

    /** @return list<PropertyChange> */
    public function changes(): array
    {
        return $this->changes;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-01-01T00:00:00+00:00');
    }

    public function entityType(): string
    {
        return 'test';
    }

    public function entityId(): string
    {
        return $this->id;
    }

    public function actionLabel(): string
    {
        return 'Test Update';
    }
}
