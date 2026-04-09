<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;

final class FakeEmailTemplateRepository implements EmailTemplateRepository
{
    /** @var list<EmailTemplate> */
    public array $created = [];

    /** @var array<string, array{subject: string, body: string}> */
    public array $updated = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, EmailTemplate> $templates keyed by "type:locale" */
    public function __construct(
        private array $templates = [],
    ) {}

    public function findByTypeAndLocale(string $type, string $locale): ?EmailTemplate
    {
        return $this->templates[$type.':'.$locale] ?? null;
    }

    /** @return list<EmailTemplate> */
    public function findAllByType(string $type): array
    {
        return array_values(array_filter(
            $this->templates,
            static fn (EmailTemplate $template): bool => $template->type->value === $type,
        ));
    }

    /** @return list<EmailTemplate> */
    public function findAll(): array
    {
        return array_values($this->templates);
    }

    public function updateContent(string $type, string $locale, string $subjectTemplate, string $bodyTemplate): void
    {
        $this->updated[$type.':'.$locale] = ['subject' => $subjectTemplate, 'body' => $bodyTemplate];
    }

    public function deleteByTypeAndLocale(string $type, string $locale): void
    {
        $this->deleted[] = $type.':'.$locale;
        unset($this->templates[$type.':'.$locale]);
    }

    public function create(EmailTemplate $emailTemplate): void
    {
        $this->created[] = $emailTemplate;
        $key = $emailTemplate->type->value.':'.$emailTemplate->locale->value;
        $this->templates[$key] = $emailTemplate;
    }
}
