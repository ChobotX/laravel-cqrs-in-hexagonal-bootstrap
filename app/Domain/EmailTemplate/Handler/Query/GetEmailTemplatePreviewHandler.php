<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplatePreviewQuery;
use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;

/** @implements QueryHandler<GetEmailTemplatePreviewQuery, RenderedEmail> */
final readonly class GetEmailTemplatePreviewHandler implements QueryHandler
{
    public function __construct(
        private TemplateCompiler $templateCompiler,
    ) {}

    public function handle(Query $query): RenderedEmail
    {
        $variables = $this->sampleVariables($query->templateType);

        return $this->templateCompiler->compile(
            $query->subjectTemplate,
            $query->bodyTemplate,
            $variables,
        );
    }

    /** @return array<string, string> */
    private function sampleVariables(string $templateType): array
    {
        $typeConfig = EmailTemplateTypes::TYPES[$templateType] ?? null;

        if ($typeConfig === null) {
            return [];
        }

        $samples = [];

        foreach ($typeConfig['variables'] as $key => $config) {
            $samples[$key] = $config['sample'];
        }

        return $samples;
    }
}
