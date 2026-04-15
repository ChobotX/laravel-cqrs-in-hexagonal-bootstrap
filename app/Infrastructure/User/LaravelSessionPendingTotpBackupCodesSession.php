<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

final readonly class LaravelSessionPendingTotpBackupCodesSession implements PendingTotpBackupCodesSession
{
    private const string SESSION_PREFIX = 'totp_pending_backup.';

    private const string DOWNLOAD_FLAG_PREFIX = 'totp_pending_backup_downloaded.';

    private const int DOWNLOAD_FLAG_TTL_HOURS = 2;

    public function remember(string $userId, array $plaintextCodes): void
    {
        Session::put(self::SESSION_PREFIX.$userId.'.codes', $plaintextCodes);
        Cache::put(self::DOWNLOAD_FLAG_PREFIX.$userId, false, now()->addHours(self::DOWNLOAD_FLAG_TTL_HOURS));
    }

    public function plaintextCodes(string $userId): ?array
    {
        $codes = Session::get(self::SESSION_PREFIX.$userId.'.codes');

        if (! is_array($codes)) {
            return null;
        }

        $normalized = [];
        foreach ($codes as $code) {
            if (is_string($code)) {
                $normalized[] = $code;
            }
        }

        return $normalized;
    }

    public function markDownloadRecorded(string $userId): void
    {
        Cache::put(self::DOWNLOAD_FLAG_PREFIX.$userId, true, now()->addHours(self::DOWNLOAD_FLAG_TTL_HOURS));
    }

    public function hasRecordedDownload(string $userId): bool
    {
        return (bool) Cache::get(self::DOWNLOAD_FLAG_PREFIX.$userId, false);
    }

    public function forget(string $userId): void
    {
        Session::forget([
            self::SESSION_PREFIX.$userId.'.codes',
        ]);

        Cache::forget(self::DOWNLOAD_FLAG_PREFIX.$userId);
    }
}
