<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Repository;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;

/**
 * Persistence port for email template data in the EmailTemplate context; implementations live in Infrastructure.
 */
interface EmailTemplateRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findByTypeAndLocale(string $type, string $locale): ?EmailTemplate;

    /**
     * @return list<EmailTemplate>
     *                             Loads a record or value object, or null when absent.
     */
    public function findAllByType(string $type): array;

    /**
     * @return list<EmailTemplate>
     *                             Loads a record or value object, or null when absent.
     */
    public function findAll(): array;

    /** Contract operation `updateContent`; see infrastructure for behavior. */
    public function updateContent(string $type, string $locale, string $subjectTemplate, string $bodyTemplate): void;

    /** Deletes or soft-deletes the targeted record. */
    public function deleteByTypeAndLocale(string $type, string $locale): void;

    /** Persists a new or updated aggregate row. */
    public function create(EmailTemplate $emailTemplate): void;
}
