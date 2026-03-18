<?php

declare(strict_types=1);

use App\Infrastructure\Auth\RequestAuthenticatedUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Tests\Helper\FakeImpersonationManager;

it('returns auth user id when not impersonating', function (): void {
    $guard = new RequestAuthenticatedUserTestGuard('user-1');
    $impersonation = new FakeImpersonationManager;

    $user = new RequestAuthenticatedUser($guard, $impersonation);

    expect($user->id())->toBe('user-1');
    expect($user->impersonatorId())->toBeNull();
    expect($user->isImpersonating())->toBeFalse();
});

it('returns impersonated user id when impersonating', function (): void {
    $guard = new RequestAuthenticatedUserTestGuard(null);
    $impersonation = new FakeImpersonationManager;
    $impersonation->start('admin-user', 'target-user');

    $user = new RequestAuthenticatedUser($guard, $impersonation);

    expect($user->id())->toBe('target-user');
    expect($user->impersonatorId())->toBe('admin-user');
    expect($user->isImpersonating())->toBeTrue();
});

it('returns null when no authenticated user', function (): void {
    $guard = new RequestAuthenticatedUserTestGuard(null);
    $impersonation = new FakeImpersonationManager;

    $user = new RequestAuthenticatedUser($guard, $impersonation);

    expect($user->id())->toBeNull();
});

final class RequestAuthenticatedUserTestGuard implements Guard
{
    private ?Authenticatable $authenticatable = null;

    public function __construct(?string $userId)
    {
        if ($userId !== null) {
            $this->authenticatable = new RequestAuthenticatedUserTestAuthenticatable($userId);
        }
    }

    public function check(): bool
    {
        return $this->authenticatable instanceof Authenticatable;
    }

    public function guest(): bool
    {
        return ! $this->authenticatable instanceof Authenticatable;
    }

    public function user(): ?Authenticatable
    {
        return $this->authenticatable;
    }

    public function id(): int|string|null
    {
        /* @var int|string|null */
        return $this->authenticatable?->getAuthIdentifier();
    }

    public function validate(array $credentials = []): bool
    {
        return false;
    }

    public function hasUser(): bool
    {
        return $this->authenticatable instanceof Authenticatable;
    }

    public function setUser(Authenticatable $user): static
    {
        $this->authenticatable = $user;

        return $this;
    }
}

final readonly class RequestAuthenticatedUserTestAuthenticatable implements Authenticatable
{
    public function __construct(private string $id) {}

    public function getAuthIdentifier(): string
    {
        return $this->id;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): string
    {
        return '';
    }

    public function setRememberToken(mixed $value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }
}
