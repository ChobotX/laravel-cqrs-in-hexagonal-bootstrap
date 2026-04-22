<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplatePreviewQuery;
use App\Presentation\Http\Request\Web\Settings\PreviewEmailTemplateRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('email_templates.templates.read')]
final readonly class PreviewEmailTemplateController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PreviewEmailTemplateRequest $previewEmailTemplateRequest): JsonResponse
    {
        $renderedEmail = $this->queryBus->dispatch(new GetEmailTemplatePreviewQuery(
            templateType: $previewEmailTemplateRequest->string('templateType')->toString(),
            locale: $previewEmailTemplateRequest->string('locale')->toString(),
            subjectTemplate: $previewEmailTemplateRequest->string('subjectTemplate')->toString(),
            bodyTemplate: $previewEmailTemplateRequest->string('bodyTemplate')->toString(),
        ));

        return new JsonResponse([
            'subject' => $renderedEmail->subject,
            'html' => $renderedEmail->htmlBody,
        ]);
    }
}
