<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface PendingTotpBackupCodesSession
{
    /**
     * @param  list<string>  $plaintextCodes
     */
    public function remember(string $userId, array $plaintextCodes): void;

    /**
     * @return list<string>|null
     */
    public function plaintextCodes(string $userId): ?array;

    public function markDownloadRecorded(string $userId): void;

    public function hasRecordedDownload(string $userId): bool;

    public function forget(string $userId): void;
}
