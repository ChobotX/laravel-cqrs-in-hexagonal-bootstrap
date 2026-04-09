<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Repository;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;

interface EmailTemplateRepository
{
    public function findByTypeAndLocale(string $type, string $locale): ?EmailTemplate;

    /**
     * @return list<EmailTemplate>
     */
    public function findAllByType(string $type): array;

    /**
     * @return list<EmailTemplate>
     */
    public function findAll(): array;

    public function updateContent(string $type, string $locale, string $subjectTemplate, string $bodyTemplate): void;

    public function deleteByTypeAndLocale(string $type, string $locale): void;

    public function create(EmailTemplate $emailTemplate): void;
}
