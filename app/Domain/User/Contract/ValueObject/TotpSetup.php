<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

final readonly class TotpSetup
{
    /**
     * @param  list<string>|null  $backupCodesPlaintext
     */
    public function __construct(
        public ?string $secret,
        public ?string $otpauthUri,
        public bool $confirmed,
        public ?array $backupCodesPlaintext,
        public bool $backupCodesDownloadRecorded,
    ) {}
}
