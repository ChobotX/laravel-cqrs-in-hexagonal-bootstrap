<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplateQuery;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateWithMetadata;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;

/** @implements QueryHandler<GetEmailTemplateQuery, EmailTemplateWithMetadata> */
final readonly class GetEmailTemplateHandler implements QueryHandler
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    public function handle(Query $query): EmailTemplateWithMetadata
    {
        $template = $this->emailTemplateRepository->findByTypeAndLocale($query->templateType, $query->locale);

        if (! $template instanceof \App\Domain\EmailTemplate\Contract\Entity\EmailTemplate) {
            throw new EmailTemplateNotFoundException($query->templateType, $query->locale);
        }

        $typeConfig = EmailTemplateTypes::TYPES[$query->templateType] ?? null;

        return new EmailTemplateWithMetadata(
            template: $template,
            typeName: $typeConfig['name'] ?? $query->templateType,
            typeDescription: $typeConfig['description'] ?? '',
            variables: $typeConfig['variables'] ?? [],
        );
    }
}
