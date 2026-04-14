<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\AdminResetUserTwoFactorCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\ValueObject\EmailTwoFactorChallenge;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;
use App\Domain\User\Handler\Command\AdminResetUserTwoFactorHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakePendingTotpBackupCodesSession;
use Tests\Helper\FakeUserRepository;
use Tests\Helper\FakeUserTwoFactorStateRepository;

it('clears two-factor state, pending totp session, and email challenges', function (): void {
    $targetId = '550e8400-e29b-41d4-a716-446655440601';
    $userId = new UserId($targetId);
    $userRepository = new FakeUserRepository([
        $targetId => new User($userId, new UserName('Locked Out'), new Email('locked@example.com'), true),
    ]);
    $stateRepository = new FakeUserTwoFactorStateRepository;
    $stateRepository->set($userId, new UserTwoFactorState(
        emailEnabled: true,
        emailConfirmedAt: new DateTimeImmutable,
        totpSecret: 'SECRET',
        totpConfirmedAt: new DateTimeImmutable,
        totpRecoveryCodeHashes: ['hash-one'],
    ));

    $challengeRepository = new class implements EmailTwoFactorChallengeRepository
    {
        public bool $purged = false;

        public function issue(UserId $userId, string $codeHash, DateTimeImmutable $expiresAt): void {}

        public function latest(UserId $userId): ?EmailTwoFactorChallenge
        {
            return null;
        }

        public function markAttempt(UserId $userId): void {}

        public function consume(UserId $userId): void {}

        public function deleteAllForUser(UserId $userId): void
        {
            $this->purged = true;
        }
    };

    $pendingSession = new FakePendingTotpBackupCodesSession;
    $pendingSession->remember($targetId, ['CODE']);

    $handler = new AdminResetUserTwoFactorHandler(
        $userRepository,
        $stateRepository,
        $challengeRepository,
        $pendingSession,
    );

    $handler->handle(new AdminResetUserTwoFactorCommand($targetId));

    $captured = $stateRepository->captured;
    expect($challengeRepository->purged)->toBeTrue()
        ->and($pendingSession->codes)->toBeNull()
        ->and($captured)->toBeInstanceOf(UserTwoFactorState::class);
    assert($captured instanceof UserTwoFactorState);
    expect($captured->emailEnabled)->toBeFalse()
        ->and($captured->emailConfirmedAt)->toBeNull()
        ->and($captured->totpSecret)->toBeNull()
        ->and($captured->totpConfirmedAt)->toBeNull()
        ->and($captured->totpRecoveryCodeHashes)->toBeNull();
});

it('throws when target user does not exist', function (): void {
    $handler = new AdminResetUserTwoFactorHandler(
        new FakeUserRepository([]),
        new FakeUserTwoFactorStateRepository,
        new class implements EmailTwoFactorChallengeRepository
        {
            public function issue(UserId $userId, string $codeHash, DateTimeImmutable $expiresAt): void {}

            public function latest(UserId $userId): ?EmailTwoFactorChallenge
            {
                return null;
            }

            public function markAttempt(UserId $userId): void {}

            public function consume(UserId $userId): void {}

            public function deleteAllForUser(UserId $userId): void {}
        },
        new FakePendingTotpBackupCodesSession,
    );

    expect(fn () => $handler->handle(new AdminResetUserTwoFactorCommand('550e8400-e29b-41d4-a716-446655440999')))
        ->toThrow(UserNotFoundException::class);
});
