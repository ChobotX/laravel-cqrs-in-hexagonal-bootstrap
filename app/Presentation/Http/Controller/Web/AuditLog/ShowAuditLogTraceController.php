<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\AuditLog;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\AuditLog\Contract\Query\GetAuditLogByTraceId\GetAuditLogByTraceIdQuery;
use Illuminate\View\View;

#[RequiresPermission('audit_log.history.read')]
final readonly class ShowAuditLogTraceController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $traceId): View
    {
        $entries = $this->queryBus->dispatch(new GetAuditLogByTraceIdQuery($traceId));

        return view('audit-log.trace', [
            'traceId' => $traceId,
            'entries' => $entries,
        ]);
    }
}
