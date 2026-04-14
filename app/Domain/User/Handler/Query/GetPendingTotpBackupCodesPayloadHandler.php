<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Query\GetPendingTotpBackupCodesPayloadQuery;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;

/** @implements QueryHandler<GetPendingTotpBackupCodesPayloadQuery, list<string>|null> */
final readonly class GetPendingTotpBackupCodesPayloadHandler implements QueryHandler
{
    public function __construct(
        private PendingTotpBackupCodesSession $pendingTotpBackupCodesSession,
    ) {}

    public function handle(Query $query): ?array
    {
        /** @var GetPendingTotpBackupCodesPayloadQuery $query */
        $codes = $this->pendingTotpBackupCodesSession->plaintextCodes($query->userId);

        if ($codes === null || $codes === []) {
            return null;
        }

        return $codes;
    }
}
