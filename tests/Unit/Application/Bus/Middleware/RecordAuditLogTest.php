<?php

declare(strict_types=1);

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\Middleware\RecordAuditLog;
use App\Application\Bus\Sensitive;
use App\Application\Bus\SkipAuditLog;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;
use App\Contract\IdGenerator;
use App\Contract\Tracing\TraceContext;
use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Service\CommandPayloadExtractor;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Tests\Helper\FakeAuditLogWriter;

it('records a successful command', function (): void {
    $writer = new FakeAuditLogWriter;
    $recordAuditLog = buildAuditMiddleware($writer, 'user-1', 'test-trace-id');

    $command = new TestAuditableCmd('entity-1', 'Test');

    $recordAuditLog->handle($command, fn (): null => null);

    expect($writer->recorded)->toHaveCount(1);

    $entry = $writer->recorded[0];
    expect($entry->status)->toBe(AuditLogStatus::Success)
        ->and($entry->traceId)->toBe('test-trace-id')
        ->and($entry->userId)->toBe('user-1')
        ->and($entry->entityType)->toBe('test')
        ->and($entry->entityId)->toBe('entity-1')
        ->and($entry->actionLabel)->toBe('Test Auditable Cmd');
});

it('records a failed command', function (): void {
    $writer = new FakeAuditLogWriter;
    $recordAuditLog = buildAuditMiddleware($writer, 'user-1');

    $command = new TestAuditableCmd('entity-1', 'Test');

    try {
        $recordAuditLog->handle($command, fn () => throw new RuntimeException('fail'));
    } catch (RuntimeException) {
    }

    expect($writer->recorded)->toHaveCount(1)
        ->and($writer->recorded[0]->status)->toBe(AuditLogStatus::Failure);
});

it('skips commands with SkipAuditLog attribute', function (): void {
    $writer = new FakeAuditLogWriter;
    $recordAuditLog = buildAuditMiddleware($writer, 'user-1');

    $command = new TestSkippedCmd;

    $recordAuditLog->handle($command, fn (): null => null);

    expect($writer->recorded)->toHaveCount(0);
});

it('records entity info from AuditableCommand', function (): void {
    $writer = new FakeAuditLogWriter;
    $recordAuditLog = buildAuditMiddleware($writer, 'user-1');

    $command = new TestEntityCmd('data-123');

    $recordAuditLog->handle($command, fn (): null => null);

    expect($writer->recorded)->toHaveCount(1)
        ->and($writer->recorded[0]->entityType)->toBe('test')
        ->and($writer->recorded[0]->entityId)->toBe('data-123');
});

it('masks sensitive properties', function (): void {
    $writer = new FakeAuditLogWriter;
    $recordAuditLog = buildAuditMiddleware($writer, 'user-1');

    $command = new TestSensitiveCmd('user-1', 'secret-password');

    $recordAuditLog->handle($command, fn (): null => null);

    expect($writer->recorded[0]->payload)->toBe([
        'userId' => 'user-1',
        'rawPassword' => '***',
    ]);
});

it('handles null user context', function (): void {
    $writer = new FakeAuditLogWriter;
    $recordAuditLog = buildAuditMiddleware($writer, null);

    $command = new TestAuditableCmd('entity-1', 'Test');

    $recordAuditLog->handle($command, fn (): null => null);

    expect($writer->recorded[0]->userId)->toBeNull();
});

function buildAuditMiddleware(FakeAuditLogWriter $fakeAuditLogWriter, ?string $userId, string $traceId = ''): RecordAuditLog
{
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

    $idGenerator = new class implements IdGenerator
    {
        public function generate(): string
        {
            return '550e8400-e29b-41d4-a716-446655440000';
        }
    };

    $traceContext = new readonly class($traceId) implements TraceContext
    {
        public function __construct(private ?string $traceId) {}

        public function traceId(): ?string
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

    return new RecordAuditLog(
        auditLogWriter: $fakeAuditLogWriter,
        authenticatedUser: $authenticatedUser,
        idGenerator: $idGenerator,
        commandPayloadExtractor: new CommandPayloadExtractor,
        traceContext: $traceContext,
    );
}

#[RequiresPermission('test.read')]
final readonly class TestAuditableCmd implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public function auditEntityType(): string
    {
        return 'test';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}

#[SkipAuditLog(reason: 'Test skip')]
#[RequiresPermission('test.read')]
final readonly class TestSkippedCmd implements Command {}

#[RequiresPermission('test.read')]
final readonly class TestEntityCmd implements AuditableCommand, Command
{
    public function __construct(
        public string $data,
    ) {}

    public function auditEntityType(): string
    {
        return 'test';
    }

    public function auditEntityId(): string
    {
        return $this->data;
    }
}

#[RequiresPermission('test.read')]
final readonly class TestSensitiveCmd implements AuditableCommand, Command
{
    public function __construct(
        public string $userId,
        #[Sensitive]
        public string $rawPassword,
    ) {}

    public function auditEntityType(): string
    {
        return 'user';
    }

    public function auditEntityId(): string
    {
        return $this->userId;
    }
}
