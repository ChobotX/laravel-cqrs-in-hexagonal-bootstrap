<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Contract\Event\EventCollector;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Command\EnableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Command\VerifyTwoFactorChallengeCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\EmailTwoFactorChallenge;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;
use App\Domain\User\Exception\InvalidTwoFactorCodeException;
use App\Domain\User\Exception\TotpBackupCodesDownloadRequiredException;
use App\Domain\User\Handler\Command\ConfirmTotpSetupHandler;
use App\Domain\User\Handler\Command\DisableEmailTwoFactorHandler;
use App\Domain\User\Handler\Command\DisableTotpTwoFactorHandler;
use App\Domain\User\Handler\Command\EnableEmailTwoFactorHandler;
use App\Domain\User\Handler\Command\IssueEmailTwoFactorChallengeHandler;
use App\Domain\User\Handler\Command\StartTotpSetupHandler;
use App\Domain\User\Handler\Command\VerifyTwoFactorChallengeHandler;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Handler\Query\GetTotpSetupHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakePendingTotpBackupCodesSession;
use Tests\Helper\FakeQueryBus;
use Tests\Helper\FakeUserRepository;
use Tests\Helper\FakeUserTwoFactorStateRepository;

it('covers two-factor command handlers and totp setup query handler', function (): void {
    $stateRepository = new FakeUserTwoFactorStateRepository;
    $pendingBackupSession = new FakePendingTotpBackupCodesSession;
    $userId = new UserId('550e8400-e29b-41d4-a716-446655440777');
    $stateRepository->set($userId, new UserTwoFactorState(false, null, null, null));

    $manager = new class implements TwoFactorManager
    {
        public function generateTotpSecret(): string
        {
            return 'SECRET';
        }

        public function buildTotpUri(string $issuer, string $accountName, string $secret): string
        {
            return 'otpauth://'.$issuer.'/'.$accountName.'/'.$secret;
        }

        public function verifyTotpCode(string $secret, string $code): bool
        {
            return $secret === 'SECRET' && $code === '123456';
        }

        public function generateEmailCode(): string
        {
            return '123456';
        }

        public function hashChallengeCode(string $code): string
        {
            return 'hash-'.$code;
        }

        public function verifyChallengeCode(string $plainCode, string $hash): bool
        {
            return $hash === 'hash-'.$plainCode;
        }

        public function generateTotpRecoveryCodes(): array
        {
            return ['AAAA-BBBB-CCCC-DDDD'];
        }

        public function hashTotpRecoveryCode(string $plain): string
        {
            return 'h:'.$plain;
        }

        public function verifyTotpRecoveryCode(string $plain, string $hash): bool
        {
            return $hash === 'h:'.$plain;
        }
    };

    $challengeRepository = new class implements EmailTwoFactorChallengeRepository
    {
        public ?EmailTwoFactorChallenge $challenge = null;

        public bool $consumed = false;

        public function issue(UserId $userId, string $codeHash, DateTimeImmutable $expiresAt): void
        {
            $this->challenge = new EmailTwoFactorChallenge($codeHash, $expiresAt, 0, false);
        }

        public function latest(UserId $userId): ?EmailTwoFactorChallenge
        {
            return $this->challenge;
        }

        public function markAttempt(UserId $userId): void {}

        public function consume(UserId $userId): void
        {
            $this->consumed = true;
        }

        public function deleteAllForUser(UserId $userId): void {}
    };

    $commandBus = new FakeCommandBus;

    $eventCollector = new class implements EventCollector
    {
        /** @var list<DomainEvent> */
        private array $events = [];

        public function collect(DomainEvent ...$events): void
        {
            foreach ($events as $event) {
                $this->events[] = $event;
            }
        }

        public function peek(): array
        {
            return $this->events;
        }

        public function flush(): array
        {
            $events = $this->events;
            $this->events = [];

            return $events;
        }
    };

    $knownUser = new User($userId, new UserName('A'), new Email('a@example.com'));
    $userRepository = new FakeUserRepository([$userId->value => $knownUser]);
    $userByIdBus = new FakeQueryBus([
        GetUserByIdQuery::class => fn (GetUserByIdQuery $query): User => $query->id === $userId->value
            ? $knownUser
            : throw new UserNotFoundException($query->id),
    ]);

    new StartTotpSetupHandler($stateRepository, $manager, $pendingBackupSession)->handle(new StartTotpSetupCommand($userId->value));
    $pendingBackupSession->markDownloadRecorded($userId->value);
    new ConfirmTotpSetupHandler($stateRepository, $manager, $pendingBackupSession)->handle(new ConfirmTotpSetupCommand($userId->value, '123456'));
    new EnableEmailTwoFactorHandler($stateRepository)->handle(new EnableEmailTwoFactorCommand($userId->value));
    new DisableEmailTwoFactorHandler($stateRepository)->handle(new DisableEmailTwoFactorCommand($userId->value));
    new DisableTotpTwoFactorHandler($stateRepository, $pendingBackupSession)->handle(new DisableTotpTwoFactorCommand($userId->value));
    $translator = new class implements App\Contract\Translation\Translator
    {
        public function translate(string $key, array $replace = [], ?string $locale = null): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    new IssueEmailTwoFactorChallengeHandler($challengeRepository, $commandBus, $userByIdBus, $manager, $translator)
        ->handle(new IssueEmailTwoFactorChallengeCommand($userId->value));
    new VerifyTwoFactorChallengeHandler($stateRepository, $challengeRepository, $manager)
        ->handle(new VerifyTwoFactorChallengeCommand($userId->value, 'email', '123456'));

    $totpSetup = new GetTotpSetupHandler($stateRepository, $userRepository, $manager, $pendingBackupSession)
        ->handle(new GetTotpSetupQuery($userId->value));

    expect($commandBus->dispatched)->not->toBeEmpty()
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SendTemplatedEmailCommand::class)
        ->and($challengeRepository->consumed)->toBeTrue()
        ->and($totpSetup->confirmed)->toBeFalse();
});

it('covers exceptional and alternate branches in two-factor handlers', function (): void {
    $stateRepository = new FakeUserTwoFactorStateRepository;
    $userId = new UserId('550e8400-e29b-41d4-a716-446655440778');
    $stateRepository->set($userId, new UserTwoFactorState(false, null, 'SECRET', null));

    $manager = new class implements TwoFactorManager
    {
        public function generateTotpSecret(): string
        {
            return 'SECRET';
        }

        public function buildTotpUri(string $issuer, string $accountName, string $secret): string
        {
            return 'otpauth://'.$issuer.'/'.$accountName.'/'.$secret;
        }

        public function verifyTotpCode(string $secret, string $code): bool
        {
            return false;
        }

        public function generateEmailCode(): string
        {
            return '123456';
        }

        public function hashChallengeCode(string $code): string
        {
            return 'hash-'.$code;
        }

        public function verifyChallengeCode(string $plainCode, string $hash): bool
        {
            return false;
        }

        public function generateTotpRecoveryCodes(): array
        {
            return ['ZZZZ-YYYY-XXXX-WWWW'];
        }

        public function hashTotpRecoveryCode(string $plain): string
        {
            return 'h:'.$plain;
        }

        public function verifyTotpRecoveryCode(string $plain, string $hash): bool
        {
            return $hash === 'h:'.$plain;
        }
    };

    $challengeRepository = new class implements EmailTwoFactorChallengeRepository
    {
        public function issue(UserId $userId, string $codeHash, DateTimeImmutable $expiresAt): void {}

        public function latest(UserId $userId): EmailTwoFactorChallenge
        {
            return new EmailTwoFactorChallenge('hash-123456', new DateTimeImmutable('-1 minute'), 0, false);
        }

        public function markAttempt(UserId $userId): void {}

        public function consume(UserId $userId): void {}

        public function deleteAllForUser(UserId $userId): void {}
    };

    $missingUserRepository = new FakeUserRepository;
    $commandBus = new FakeCommandBus;
    $missingUserBus = new FakeQueryBus([
        GetUserByIdQuery::class => fn (GetUserByIdQuery $query): User => throw new UserNotFoundException($query->id),
    ]);

    $eventCollector = new class implements EventCollector
    {
        /** @var list<DomainEvent> */
        private array $events = [];

        public function collect(DomainEvent ...$events): void
        {
            foreach ($events as $event) {
                $this->events[] = $event;
            }
        }

        public function peek(): array
        {
            return $this->events;
        }

        public function flush(): array
        {
            $events = $this->events;
            $this->events = [];

            return $events;
        }
    };

    // user missing branch in issue handler
    $translator = new class implements App\Contract\Translation\Translator
    {
        public function translate(string $key, array $replace = [], ?string $locale = null): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    new IssueEmailTwoFactorChallengeHandler($challengeRepository, $commandBus, $missingUserBus, $manager, $translator)
        ->handle(new IssueEmailTwoFactorChallengeCommand($userId->value));
    expect($commandBus->dispatched)->toBeEmpty();

    $pendingSession = new FakePendingTotpBackupCodesSession;
    new StartTotpSetupHandler($stateRepository, $manager, $pendingSession)->handle(new StartTotpSetupCommand($userId->value));

    expect(fn () => new ConfirmTotpSetupHandler($stateRepository, $manager, $pendingSession)
        ->handle(new ConfirmTotpSetupCommand($userId->value, '123456')))
        ->toThrow(TotpBackupCodesDownloadRequiredException::class);

    $pendingSession->markDownloadRecorded($userId->value);
    expect(fn () => new ConfirmTotpSetupHandler($stateRepository, $manager, $pendingSession)
        ->handle(new ConfirmTotpSetupCommand($userId->value, '000000')))
        ->toThrow(InvalidTwoFactorCodeException::class);

    // invalid challenge branch
    expect(fn () => new VerifyTwoFactorChallengeHandler($stateRepository, $challengeRepository, $manager)
        ->handle(new VerifyTwoFactorChallengeCommand($userId->value, 'email', '000000')))
        ->toThrow(InvalidTwoFactorCodeException::class);

    // invalid totp challenge branch
    expect(fn () => new VerifyTwoFactorChallengeHandler($stateRepository, $challengeRepository, $manager)
        ->handle(new VerifyTwoFactorChallengeCommand($userId->value, 'totp', '000000')))
        ->toThrow(InvalidTwoFactorCodeException::class);

    // successful totp challenge branch
    $successManager = new class implements TwoFactorManager
    {
        public function generateTotpSecret(): string
        {
            return 'SECRET';
        }

        public function buildTotpUri(string $issuer, string $accountName, string $secret): string
        {
            return '';
        }

        public function verifyTotpCode(string $secret, string $code): bool
        {
            return true;
        }

        public function generateEmailCode(): string
        {
            return '123456';
        }

        public function hashChallengeCode(string $code): string
        {
            return 'hash-'.$code;
        }

        public function verifyChallengeCode(string $plainCode, string $hash): bool
        {
            return true;
        }

        public function generateTotpRecoveryCodes(): array
        {
            return ['1111-2222-3333-4444'];
        }

        public function hashTotpRecoveryCode(string $plain): string
        {
            return 'h:'.$plain;
        }

        public function verifyTotpRecoveryCode(string $plain, string $hash): bool
        {
            return $hash === 'h:'.$plain;
        }
    };
    new VerifyTwoFactorChallengeHandler($stateRepository, $challengeRepository, $successManager)
        ->handle(new VerifyTwoFactorChallengeCommand($userId->value, 'totp', '123456'));

    // get totp setup branches with missing secret and missing user
    $emptyStateRepository = new FakeUserTwoFactorStateRepository;
    $totpSetup = new GetTotpSetupHandler($emptyStateRepository, $missingUserRepository, $manager, new FakePendingTotpBackupCodesSession)
        ->handle(new GetTotpSetupQuery($userId->value));
    expect($totpSetup->secret)->toBeNull();

    $gtsPending = new FakePendingTotpBackupCodesSession;
    $gtsPending->remember($userId->value, ['CODE-ONE']);

    $emptyStateRepository->set($userId, new UserTwoFactorState(false, null, 'SECRET', null));
    $setupWithMissingUser = new GetTotpSetupHandler($emptyStateRepository, $missingUserRepository, $manager, $gtsPending)
        ->handle(new GetTotpSetupQuery($userId->value));
    expect($setupWithMissingUser->otpauthUri)->toContain($userId->value)
        ->and($setupWithMissingUser->backupCodesPlaintext)->toBe(['CODE-ONE'])
        ->and($setupWithMissingUser->backupCodesDownloadRecorded)->toBeFalse();
});
