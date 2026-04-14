<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;

final class FakePendingTotpBackupCodesSession implements PendingTotpBackupCodesSession
{
    public bool $downloaded = false;

    /** @var list<string>|null */
    public ?array $codes = null;

    public function remember(string $userId, array $plaintextCodes): void
    {
        $this->codes = $plaintextCodes;
        $this->downloaded = false;
    }

    public function plaintextCodes(string $userId): ?array
    {
        return $this->codes;
    }

    public function markDownloadRecorded(string $userId): void
    {
        $this->downloaded = true;
    }

    public function hasRecordedDownload(string $userId): bool
    {
        return $this->downloaded;
    }

    public function forget(string $userId): void
    {
        $this->codes = null;
        $this->downloaded = false;
    }
}
