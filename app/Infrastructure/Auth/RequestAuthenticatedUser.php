<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Authorization\Contract\Service\ImpersonationManager;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Domain\User\Contract\ValueObject\UserId;
use Illuminate\Contracts\Auth\Guard;

final readonly class RequestAuthenticatedUser implements AuthenticatedUser
{
    public function __construct(
        private Guard $guard,
        private ImpersonationManager $impersonationManager,
        private UserRepository $userRepository,
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

    public function name(): ?string
    {
        $userId = $this->id();

        if ($userId === null) {
            return null;
        }

        if ($this->impersonationManager->isActive()) {
            return $this->userRepository->findById(new UserId($userId))?->name->value;
        }

        /** @var \App\Infrastructure\Eloquent\User\UserModel|null $user */
        $user = $this->guard->user();

        return $user?->name;
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
