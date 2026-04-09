<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\EmailTemplate\Contract\Query\ListEmailTemplatesQuery;
use Illuminate\View\View;

#[RequiresPermission('email_templates.templates.read')]
final readonly class ListEmailTemplatesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $templates = $this->queryBus->dispatch(new ListEmailTemplatesQuery);

        return view('settings.email-templates.index', [
            'templates' => $templates,
        ]);
    }
}
