<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Application\Event\PropertyChangeBuilder;
use App\Domain\User\Constant\UserFields;
use App\Domain\User\Contract\Command\UpdateProfileCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\UserUpdated;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\Exception\InvalidUserDataException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\UpdateProfileHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

/** @param list<array{userId: string, rawPassword: string}> $calls */
function profilePasswordManager(array &$calls = []): PasswordManager
{
    return new class($calls) implements PasswordManager
    {
        /** @param list<array{userId: string, rawPassword: string}> $calls */
        public function __construct(public array &$calls) {}

        public function setPassword(string $userId, string $rawPassword): void
        {
            $this->calls[] = ['userId' => $userId, 'rawPassword' => $rawPassword];
        }
    };
}

function createProfileHandler(
    FakeUserRepository $fakeUserRepository,
    FakeEventCollector $fakeEventCollector,
    bool $hasUserUpdatePermission = false,
    ?PasswordManager $passwordManager = null,
): UpdateProfileHandler {
    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];

    return new UpdateProfileHandler(
        $fakeUserRepository,
        $passwordManager ?? profilePasswordManager($calls),
        new FakeAuthorizationChecker(
            $hasUserUpdatePermission ? ['users.list.update'] : [],
        ),
        $fakeEventCollector,
        new PropertyChangeBuilder,
    );
}

function existingUser(): User
{
    return new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );
}

it('updates name and keeps email unchanged', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->name->value)->toBe('Jane Doe')
        ->and($repository->saved[0]->email->value)->toBe('john@example.com');
});

it('sets password when provided', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];
    $updateProfileHandler = createProfileHandler($repository, $eventCollector, passwordManager: profilePasswordManager($calls));

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: 'new-password-123',
    ));

    expect($calls)->toHaveCount(1)
        ->and($calls[0]['rawPassword'])->toBe('new-password-123');
});

it('emits PasswordChanged event when password is set', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: 'new-password-123',
    ));

    $passwordEvents = array_filter(
        $eventCollector->collected,
        fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof PasswordChanged,
    );
    expect($passwordEvents)->toHaveCount(1);
});

it('skips password when null', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];
    $updateProfileHandler = createProfileHandler($repository, $eventCollector, passwordManager: profilePasswordManager($calls));

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
    ));

    expect($calls)->toBeEmpty();
});

it('skips password when empty string', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];
    $updateProfileHandler = createProfileHandler($repository, $eventCollector, passwordManager: profilePasswordManager($calls));

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: '',
    ));

    expect($calls)->toBeEmpty();
});

it('collects UserUpdated event with changes', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
    ));

    $userUpdatedEvents = array_values(array_filter(
        $eventCollector->collected,
        static fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof UserUpdated,
    ));
    expect($userUpdatedEvents)->toHaveCount(1);
    expect($userUpdatedEvents[0]->changes())->toEqual([
        new PropertyChange(UserFields::NAME, 'John Doe', 'Jane Doe'),
    ]);
});

it('does not collect UserUpdated when only password changes', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: 'new-password-123',
    ));

    $userUpdatedEvents = array_filter(
        $eventCollector->collected,
        static fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof UserUpdated,
    );
    $passwordChangedEvents = array_filter(
        $eventCollector->collected,
        static fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof PasswordChanged,
    );
    expect($userUpdatedEvents)->toHaveCount(0)
        ->and($passwordChangedEvents)->toHaveCount(1)
        ->and($repository->saved)->toHaveCount(0);
});

it('does not save or collect any event when nothing changes', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
    ));

    expect($repository->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
    ));
})->throws(UserNotFoundException::class);

it('throws when name is empty', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: '',
    ));
})->throws(InvalidUserDataException::class, 'User name must not be empty.');

it('updates email when user has users.list.update permission', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector, hasUserUpdatePermission: true);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'new@example.com',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->email->value)->toBe('new@example.com');
});

it('ignores email when user lacks users.list.update permission', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector, hasUserUpdatePermission: false);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        email: 'new@example.com',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->email->value)->toBe('john@example.com');
});

it('ignores email when email is null', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector, hasUserUpdatePermission: true);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->email->value)->toBe('john@example.com');
});

it('sets avatar when avatarFileId is provided', function (): void {
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => existingUser()]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        avatarFileId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->avatarFileId?->value)->toBe('00000000-0000-0000-0000-000000000001');
});

it('removes avatar when avatarFileId is null', function (): void {
    $userWithAvatar = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        avatarFileId: new App\Domain\File\Contract\ValueObject\FileId('00000000-0000-0000-0000-000000000002'),
    );
    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $userWithAvatar]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        avatarFileId: null,
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->avatarFileId)->toBeNull();
});

it('throws EmailAlreadyExistsException when email is taken', function (): void {
    $user = existingUser();
    $otherUser = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440001'),
        new UserName('Other User'),
        new Email('taken@example.com'),
    );

    $repository = new FakeUserRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $user,
        '550e8400-e29b-41d4-a716-446655440001' => $otherUser,
    ]);
    $eventCollector = new FakeEventCollector;

    $updateProfileHandler = createProfileHandler($repository, $eventCollector, hasUserUpdatePermission: true);

    $updateProfileHandler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'taken@example.com',
    ));
})->throws(EmailAlreadyExistsException::class);
