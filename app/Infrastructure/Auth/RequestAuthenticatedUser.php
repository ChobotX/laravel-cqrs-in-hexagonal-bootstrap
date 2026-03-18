<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\ImpersonationManager;
use Illuminate\Contracts\Auth\Guard;

final readonly class RequestAuthenticatedUser implements AuthenticatedUser
{
    public function __construct(
        private Guard $guard,
        private ImpersonationManager $impersonationManager,
    ) {}

    public function id(): ?string
    {
        if ($this->impersonationManager->isActive()) {
            return $this->impersonationManager->impersonatedUserId();
        }

        $user = $this->guard->user();

        if ($user === null) {
            return null;
        }

        /** @var string|null $id */
        $id = $user->getAuthIdentifier();

        return $id;
    }

    public function impersonatorId(): ?string
    {
        if (! $this->impersonationManager->isActive()) {
            return null;
        }

        return $this->impersonationManager->realUserId();
    }

    public function isImpersonating(): bool
    {
        return $this->impersonationManager->isActive();
    }
}
