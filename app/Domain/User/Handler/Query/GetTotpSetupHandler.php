<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\TotpSetup;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements QueryHandler<GetTotpSetupQuery, TotpSetup> */
final readonly class GetTotpSetupHandler implements QueryHandler
{
    public function __construct(
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
        private UserRepository $userRepository,
        private TwoFactorManager $twoFactorManager,
        private PendingTotpBackupCodesSession $pendingTotpBackupCodesSession,
    ) {}

    public function handle(Query $query): TotpSetup
    {
        $userId = new UserId($query->userId);
        $userTwoFactorState = $this->userTwoFactorStateRepository->get($userId);

        if ($userTwoFactorState->totpSecret === null) {
            return new TotpSetup(null, null, false, null, false);
        }

        $user = $this->userRepository->findById($userId);
        $accountName = $user?->email->value ?? $query->userId;
        $otpauthUri = $this->twoFactorManager->buildTotpUri('LaravelCQRS', $accountName, $userTwoFactorState->totpSecret);
        $confirmed = $userTwoFactorState->totpConfirmedAt instanceof DateTimeImmutable;
        $pending = ! $confirmed;
        $backupPlaintext = $pending ? $this->pendingTotpBackupCodesSession->plaintextCodes($query->userId) : null;
        $downloadRecorded = $pending
            ? $this->pendingTotpBackupCodesSession->hasRecordedDownload($query->userId)
            : true;

        return new TotpSetup(
            secret: $userTwoFactorState->totpSecret,
            otpauthUri: $otpauthUri,
            confirmed: $confirmed,
            backupCodesPlaintext: $backupPlaintext,
            backupCodesDownloadRecorded: $downloadRecorded,
        );
    }
}
