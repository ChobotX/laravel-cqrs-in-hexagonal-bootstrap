<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Repository;

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;

interface EmailLogRepository
{
    public function create(EmailLog $emailLog): void;

    /**
     * @return list<EmailLog>
     */
    public function findByRecipient(string $recipientId, int $limit, int $offset): array;

    /**
     * @return list<EmailLog>
     */
    public function findAll(int $limit, int $offset): array;

    public function countAll(): int;
}
