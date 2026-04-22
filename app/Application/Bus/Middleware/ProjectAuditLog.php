<?php

declare(strict_types=1);

namespace App\Application\Bus\Middleware;

use App\Application\Bus\SensitiveDataMasker;
use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\BusMiddleware;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EventCollector;
use App\Contract\IdGenerator;
use App\Contract\Tracing\TraceContext;
use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use Closure;
use Illuminate\Http\Request;

final readonly class ProjectAuditLog implements BusMiddleware
{
    public function __construct(
        private EventCollector $eventCollector,
        private AuditLogWriter $auditLogWriter,
        private AuthenticatedUser $authenticatedUser,
        private IdGenerator $idGenerator,
        private TraceContext $traceContext,
        private ?Request $request = null,
    ) {}

    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
    public function handle(object $message, Closure $next): mixed
    {
        $result = $next($message);

        foreach ($this->eventCollector->peek() as $domainEvent) {
            $this->writeEntry($domainEvent);
        }

        return $result;
    }

    private function writeEntry(DomainEvent $domainEvent): void
    {
        $this->auditLogWriter->record(new AuditLogEntry(
            id: new AuditLogId($this->idGenerator->generate()),
            traceId: $this->traceContext->traceId() ?? '',
            userId: $this->authenticatedUser->id(),
            impersonatorId: $this->authenticatedUser->impersonatorId(),
            command: $domainEvent::class,
            actionLabel: $domainEvent->actionLabel(),
            entityType: $domainEvent->entityType(),
            entityId: $domainEvent->entityId(),
            payload: SensitiveDataMasker::mask($domainEvent),
            changes: $domainEvent instanceof EntityUpdated
                ? $this->serializeChanges($domainEvent->changes())
                : [],
            status: AuditLogStatus::Success,
            ipAddress: $this->request?->ip(),
            occurredAt: $domainEvent->occurredAt(),
        ));
    }

    /**
     * @param  list<PropertyChange>  $changes
     * @return list<array<string, mixed>>
     */
    private function serializeChanges(array $changes): array
    {
        return array_map(fn (PropertyChange $propertyChange): array => [
            'property' => $propertyChange->property,
            'old' => $propertyChange->old,
            'new' => $propertyChange->new,
            'sensitive' => $propertyChange->sensitive,
        ], $changes);
    }
}
