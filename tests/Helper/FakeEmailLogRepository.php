<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\Repository\EmailLogRepository;

final class FakeEmailLogRepository implements EmailLogRepository
{
    /** @var list<EmailLog> */
    public array $created = [];

    /** @param list<EmailLog> $logs */
    public function __construct(
        private array $logs = [],
    ) {}

    public function create(EmailLog $emailLog): void
    {
        $this->created[] = $emailLog;
        $this->logs[] = $emailLog;
    }

    /** @return list<EmailLog> */
    public function findByRecipient(string $recipientId, int $limit, int $offset): array
    {
        $filtered = array_values(array_filter(
            $this->logs,
            static fn (EmailLog $log): bool => $log->recipientId === $recipientId,
        ));

        return array_slice($filtered, $offset, $limit);
    }

    /** @return list<EmailLog> */
    public function findAll(int $limit, int $offset): array
    {
        return array_slice($this->logs, $offset, $limit);
    }

    public function countAll(): int
    {
        return count($this->logs);
    }
}
