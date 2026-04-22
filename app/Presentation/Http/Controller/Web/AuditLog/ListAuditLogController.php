<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\AuditLog;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\AuditLog\Contract\Query\ListAuditLog\ListAuditLogQuery;
use App\Presentation\Http\Request\Web\AuditLog\ListAuditLogRequest;
use Illuminate\View\View;

#[RequiresPermission('audit_log.history.read')]
final readonly class ListAuditLogController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(ListAuditLogRequest $listAuditLogRequest): View
    {
        $entries = $this->queryBus->dispatch(new ListAuditLogQuery(
            entityType: $listAuditLogRequest->entityType(),
            entityId: $listAuditLogRequest->entityId(),
            userId: $listAuditLogRequest->userId(),
            traceId: $listAuditLogRequest->traceId(),
            from: $listAuditLogRequest->from(),
            to: $listAuditLogRequest->to(),
        ));

        return view('audit-log.index', [
            'entries' => $entries,
            'filters' => [
                'entity_type' => $listAuditLogRequest->entityType(),
                'entity_id' => $listAuditLogRequest->entityId(),
                'user_id' => $listAuditLogRequest->userId(),
                'trace_id' => $listAuditLogRequest->traceId(),
                'from' => $listAuditLogRequest->input('from'),
                'to' => $listAuditLogRequest->input('to'),
            ],
        ]);
    }
}
