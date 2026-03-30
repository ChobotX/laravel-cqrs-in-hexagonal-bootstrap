<?php

declare(strict_types=1);

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AccessDecision;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Query\Query;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Authorization\AccessScope;
use App\Infrastructure\Bus\Middleware\ResolveScopeFilter;

/** @param list<string>|null $teamVisibleIds */
function buildScopeMiddleware(
    ?string $userId = 'user-1',
    string $scope = 'all',
    ?array $teamVisibleIds = null,
): ResolveScopeFilter {
    $authenticatedUser = new readonly class($userId) implements AuthenticatedUser
    {
        public function __construct(private ?string $userId) {}

        public function id(): ?string
        {
            return $this->userId;
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

    $authorizationChecker = new readonly class($scope) implements AuthorizationChecker
    {
        public function __construct(private string $scope) {}

        public function can(string $userId, string $permission): bool
        {
            return true;
        }

        public function canWithScope(string $userId, string $permission): AccessDecision
        {
            return new readonly class($this->scope) implements AccessDecision
            {
                public function __construct(private string $scope) {}

                public function granted(): bool
                {
                    return true;
                }

                public function scope(): string
                {
                    return $this->scope;
                }
            };
        }

        /** @return list<string> */
        public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
        {
            return [];
        }
    };

    $teamMembershipChecker = new readonly class($teamVisibleIds ?? []) implements TeamMembershipChecker
    {
        /** @param list<string> $visibleIds */
        public function __construct(private array $visibleIds) {}

        public function isTeamMember(string $userId, string $teamId): bool
        {
            return false;
        }

        /** @return list<string> */
        public function memberTeamIds(string $userId): array
        {
            return [];
        }

        /** @return list<string> */
        public function visibleUserIds(string $userId): array
        {
            return $this->visibleIds;
        }
    };

    return new ResolveScopeFilter($authenticatedUser, $authorizationChecker, $teamMembershipChecker);
}

function dispatchScopeQuery(ResolveScopeFilter $resolveScopeFilter, ResolveScopeFilterTestQuery $resolveScopeFilterTestQuery): ResolveScopeFilterTestQuery
{
    $captured = $resolveScopeFilterTestQuery;
    $resolveScopeFilter->handle($resolveScopeFilterTestQuery, function (object $msg) use (&$captured): string {
        $captured = $msg;

        return 'ok';
    });
    assert($captured instanceof ResolveScopeFilterTestQuery);

    return $captured;
}

it('passes through non-ScopeAwareQuery messages', function (): void {
    $resolveScopeFilter = buildScopeMiddleware();
    $called = false;

    $resolveScopeFilter->handle(new stdClass, function (object $msg) use (&$called): string {
        $called = true;

        return 'result';
    });

    expect($called)->toBeTrue();
});

it('passes through when user id is null', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(userId: null);
    $query = new ResolveScopeFilterTestQuery;

    $resolveScopeFilterTestQuery = dispatchScopeQuery($resolveScopeFilter, $query);

    expect($resolveScopeFilterTestQuery)->toBe($query);
    expect($resolveScopeFilterTestQuery->accessContext())->toBeNull();
});

it('passes through ScopeAwareQuery without RequiresPermission', function (): void {
    $resolveScopeFilter = buildScopeMiddleware();
    $query = new ResolveScopeFilterTestNoPermissionQuery;
    $captured = $query;

    $resolveScopeFilter->handle($query, function (object $msg) use (&$captured): string {
        $captured = $msg;

        return 'ok';
    });
    assert($captured instanceof ResolveScopeFilterTestNoPermissionQuery);

    expect($captured)->toBe($query);
    expect($captured->accessContext())->toBeNull();
});

it('resolves All scope with null visibleIds', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(scope: 'all');
    $resolveScopeFilterTestQuery = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestQuery);

    $context = $resolveScopeFilterTestQuery->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::All);
    expect($context->visibleIds)->toBeNull();
});

it('resolves Own scope with only current user id', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(userId: 'user-42', scope: 'own');
    $resolveScopeFilterTestQuery = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestQuery);

    $context = $resolveScopeFilterTestQuery->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::Own);
    expect($context->visibleIds)->toBe(['user-42']);
});

it('resolves Team scope with visible user ids from TeamMembershipChecker', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(
        userId: 'user-1',
        scope: 'team',
        teamVisibleIds: ['user-1', 'user-2', 'user-3'],
    );
    $resolveScopeFilterTestQuery = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestQuery);

    $context = $resolveScopeFilterTestQuery->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::Team);
    expect($context->visibleIds)->toBe(['user-1', 'user-2', 'user-3']);
});

/** @implements Query<list<string>> */
#[RequiresPermission('test.resource.read')]
final readonly class ResolveScopeFilterTestQuery implements Query, ScopeAwareQuery
{
    public function __construct(
        private ?AccessContext $accessContext = null,
    ) {}

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}

/** @implements Query<list<string>> */
#[SkipPermissionCheck(reason: 'Test fixture for no-permission passthrough')]
final readonly class ResolveScopeFilterTestNoPermissionQuery implements Query, ScopeAwareQuery
{
    public function __construct(
        private ?AccessContext $accessContext = null,
    ) {}

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}
