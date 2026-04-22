<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplateQuery;
use Illuminate\View\View;

#[RequiresPermission('email_templates.templates.read')]
final readonly class EditEmailTemplateController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $type, string $locale): View
    {
        $emailTemplateWithMetadata = $this->queryBus->dispatch(new GetEmailTemplateQuery(
            templateType: $type,
            locale: $locale,
        ));

        return view('settings.email-templates.edit', [
            'template' => $emailTemplateWithMetadata->template,
            'typeConfig' => [
                'name' => $emailTemplateWithMetadata->typeName,
                'description' => $emailTemplateWithMetadata->typeDescription,
                'variables' => $emailTemplateWithMetadata->variables,
            ],
            'type' => $type,
            'locale' => $locale,
        ]);
    }
}
