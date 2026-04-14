<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use DateTimeImmutable;

interface PasswordHistoryRepository
{
    /** @return list<string> bcrypt hashes, newest first */
    public function listRecentHashes(string $userId, int $limit): array;

    public function append(string $userId, string $passwordHash, DateTimeImmutable $createdAt): void;

    /** Keeps the newest {@see $keepCount} rows; deletes older rows for the user. */
    public function pruneToMaxEntries(string $userId, int $keepCount): void;
}
