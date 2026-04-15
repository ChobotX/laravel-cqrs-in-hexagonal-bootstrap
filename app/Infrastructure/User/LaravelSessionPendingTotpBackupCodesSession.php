<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use Illuminate\Support\Facades\Session;

final readonly class LaravelSessionPendingTotpBackupCodesSession implements PendingTotpBackupCodesSession
{
    private const string SESSION_PREFIX = 'totp_pending_backup.';

    public function remember(string $userId, array $plaintextCodes): void
    {
        Session::put(self::SESSION_PREFIX.$userId.'.codes', $plaintextCodes);
        Session::put(self::SESSION_PREFIX.$userId.'.downloaded', false);
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
        Session::put(self::SESSION_PREFIX.$userId.'.downloaded', true);
        Session::save();
    }

    public function hasRecordedDownload(string $userId): bool
    {
        return (bool) Session::get(self::SESSION_PREFIX.$userId.'.downloaded', false);
    }

    public function forget(string $userId): void
    {
        Session::forget([
            self::SESSION_PREFIX.$userId.'.codes',
            self::SESSION_PREFIX.$userId.'.downloaded',
        ]);
    }
}
