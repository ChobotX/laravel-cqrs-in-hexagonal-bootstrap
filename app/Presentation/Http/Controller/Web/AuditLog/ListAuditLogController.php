<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\AuditLog;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\AuditLog\Contract\Query\ListAuditLog\ListAuditLogQuery;
use App\Presentation\Http\Request\Web\AuditLog\ListAuditLogRequest;
use Illuminate\View\View;

#[RequiresPermission('audit_log.history.read')]
final readonly class ListAuditLogController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(ListAuditLogRequest $request): View
    {
        $entries = $this->queryBus->dispatch(new ListAuditLogQuery(
            entityType: $request->entityType(),
            entityId: $request->entityId(),
            userId: $request->userId(),
            traceId: $request->traceId(),
            from: $request->from(),
            to: $request->to(),
        ));

        return view('audit-log.index', [
            'entries' => $entries,
            'filters' => [
                'entity_type' => $request->entityType(),
                'entity_id' => $request->entityId(),
                'user_id' => $request->userId(),
                'trace_id' => $request->traceId(),
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ]);
    }
}
