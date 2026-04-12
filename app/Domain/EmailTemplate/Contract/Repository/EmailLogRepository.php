<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Repository;

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;

/**
 * Persistence port for email log data in the EmailTemplate context; implementations live in Infrastructure.
 */
interface EmailLogRepository
{
    /** Persists a new or updated aggregate row. */
    public function create(EmailLog $emailLog): void;

    /**
     * @return list<EmailLog>
     *                        Loads a record or value object, or null when absent.
     */
    public function findByRecipient(string $recipientId, int $limit, int $offset): array;

    /**
     * @return list<EmailLog>
     *                        Loads a record or value object, or null when absent.
     */
    public function findAll(int $limit, int $offset): array;

    /** Returns the number of matching rows. */
    public function countAll(): int;
}
