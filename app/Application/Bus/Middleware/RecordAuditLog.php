<?php

declare(strict_types=1);

namespace App\Application\Bus\Middleware;

use App\Application\Bus\SensitiveDataMasker;
use App\Application\Bus\SkipAuditLog;
use App\Contract\Bus\Middleware;
use App\Contract\Command\AuditableCommand;
use App\Contract\IdGenerator;
use App\Contract\Tracing\TraceContext;
use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\Service\CommandPayloadExtractor;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Closure;
use DateTimeImmutable;
use Illuminate\Http\Request;
use ReflectionClass;
use Throwable;

final readonly class RecordAuditLog implements Middleware
{
    public function __construct(
        private AuditLogWriter $auditLogWriter,
        private AuthenticatedUser $authenticatedUser,
        private IdGenerator $idGenerator,
        private CommandPayloadExtractor $commandPayloadExtractor,
        private TraceContext $traceContext,
        private ?Request $request = null,
    ) {}

    public function handle(object $message, Closure $next): mixed
    {
        if ($this->shouldSkip($message)) {
            return $next($message);
        }

        try {
            $result = $next($message);
            $status = AuditLogStatus::Success;
        } catch (Throwable $exception) {
            $status = AuditLogStatus::Failure;
            $this->writeEntry($message, $status);

            throw $exception;
        }

        $this->writeEntry($message, $status);

        return $result;
    }

    private function shouldSkip(object $message): bool
    {
        return (new ReflectionClass($message))->getAttributes(SkipAuditLog::class) !== [];
    }

    private function writeEntry(object $message, AuditLogStatus $status): void
    {
        $entityType = null;
        $entityId = null;

        if ($message instanceof AuditableCommand) {
            $entityType = $message->auditEntityType();
            $entityId = $message->auditEntityId();
        }

        $this->auditLogWriter->record(new AuditLogEntry(
            id: new AuditLogId($this->idGenerator->generate()),
            traceId: $this->traceContext->traceId() ?? '',
            userId: $this->authenticatedUser->id(),
            impersonatorId: $this->authenticatedUser->impersonatorId(),
            command: $message::class,
            actionLabel: $this->commandPayloadExtractor->deriveActionLabel($message),
            entityType: $entityType,
            entityId: $entityId,
            payload: SensitiveDataMasker::mask($message),
            status: $status,
            ipAddress: $this->request?->ip(),
            occurredAt: new DateTimeImmutable,
        ));
    }
}
