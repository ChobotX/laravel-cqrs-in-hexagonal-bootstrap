<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\EmailTemplate\Contract\Query\ListEmailLogsQuery;
use App\Presentation\Http\Request\Web\Settings\ListEmailLogsRequest;
use Illuminate\View\View;

#[RequiresPermission('email_templates.logs.read')]
final readonly class ListEmailLogsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(ListEmailLogsRequest $listEmailLogsRequest): View
    {
        $paginatedResult = $this->queryBus->dispatch(new ListEmailLogsQuery(
            templateType: $listEmailLogsRequest->templateType(),
            recipientId: $listEmailLogsRequest->recipientId(),
        ));

        return view('settings.email-logs.index', [
            'logs' => $paginatedResult,
        ]);
    }
}
