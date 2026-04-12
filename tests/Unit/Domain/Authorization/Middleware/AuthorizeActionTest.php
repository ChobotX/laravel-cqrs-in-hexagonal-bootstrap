<?php

declare(strict_types=1);

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;
use App\Domain\Authorization\Contract\Exception\PermissionDeniedException;
use App\Domain\Authorization\Contract\Service\AccessDecision;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Authorization\Middleware\AuthorizeAction;
use App\Domain\User\Contract\Service\AuthenticatedUser;

function buildTestMiddleware(
    ?string $userId = 'user-1',
    bool $canResult = true,
): AuthorizeAction {
    $authenticatedUser = new readonly class($userId) implements AuthenticatedUser
    {
        public function __construct(private ?string $userId) {}

        public function id(): ?string
        {
            return $this->userId;
        }

        public function name(): ?string
        {
            return null;
        }

        public function impersonatorId(): ?string
        {
            return null;
        }

        public function isImpersonating(): bool
        {
            return false;
        }
    };

    $authorizationChecker = new readonly class($canResult) implements AuthorizationChecker
    {
        public function __construct(private bool $result) {}

        public function can(string $userId, string $permission): bool
        {
            return $this->result;
        }

        public function canWithScope(string $userId, string $permission): AccessDecision
        {
            throw new RuntimeException('not implemented');
        }

        /** @return list<string> */
        public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
        {
            return [];
        }
    };

    return new AuthorizeAction($authenticatedUser, $authorizationChecker);
}

it('passes through when no RequiresPermission attribute', function (): void {
    $authorizeAction = buildTestMiddleware();
    $called = false;

    $authorizeAction->handle(new stdClass, function () use (&$called): string {
        $called = true;

        return 'result';
    });

    expect($called)->toBeTrue();
});

it('passes through when user id is null', function (): void {
    $authorizeAction = buildTestMiddleware(userId: null);
    $called = false;

    $authorizeAction->handle(new AuthorizeActionTestPermissionMessage, function () use (&$called): string {
        $called = true;

        return 'result';
    });

    expect($called)->toBeTrue();
});

it('throws when permission is denied', function (): void {
    $authorizeAction = buildTestMiddleware(canResult: false);

    $authorizeAction->handle(new AuthorizeActionTestPermissionMessage, fn (): string => 'result');
})->throws(PermissionDeniedException::class);

it('allows when permission is granted', function (): void {
    $authorizeAction = buildTestMiddleware(canResult: true);
    $called = false;

    $authorizeAction->handle(new AuthorizeActionTestPermissionMessage, function () use (&$called): string {
        $called = true;

        return 'result';
    });

    expect($called)->toBeTrue();
});

#[RequiresPermission('users.list.read')]
final readonly class AuthorizeActionTestPermissionMessage implements Command {}
