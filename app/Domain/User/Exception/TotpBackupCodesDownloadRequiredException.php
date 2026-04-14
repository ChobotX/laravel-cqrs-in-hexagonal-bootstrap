<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class TotpBackupCodesDownloadRequiredException extends RuntimeException implements DomainException
{
    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.settings.totp_backup_codes_download_required');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
