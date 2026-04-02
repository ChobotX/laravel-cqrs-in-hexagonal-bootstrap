<?php

declare(strict_types=1);

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AccessDecision;
use App\Contract\Authorization\AccessScope;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Query\Query;
use App\Domain\Authorization\Middleware\ResolveScopeFilter;
use App\Domain\Team\Contract\TeamMembershipChecker;

/**
 * @param  list<string>|null  $teamVisibleUserIds
 * @param  list<string>|null  $memberTeamIds
 */
function buildScopeMiddleware(
    ?string $userId = 'user-1',
    string $scope = 'all',
    ?array $teamVisibleUserIds = null,
    ?array $memberTeamIds = null,
): ResolveScopeFilter {
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

    $teamMembershipChecker = new readonly class($teamVisibleUserIds ?? [], $memberTeamIds ?? []) implements TeamMembershipChecker
    {
        /**
         * @param  list<string>  $visibleUserIds
         * @param  list<string>  $teamIds
         */
        public function __construct(
            private array $visibleUserIds,
            private array $teamIds,
        ) {}

        public function isTeamMember(string $userId, string $teamId): bool
        {
            return false;
        }

        /** @return list<string> */
        public function memberTeamIds(string $userId): array
        {
            return $this->teamIds;
        }

        /** @return list<string> */
        public function visibleUserIds(string $userId): array
        {
            return $this->visibleUserIds;
        }
    };

    return new ResolveScopeFilter($authenticatedUser, $authorizationChecker, $teamMembershipChecker);
}

function dispatchScopeQuery(ResolveScopeFilter $resolveScopeFilter, object $query): object
{
    $captured = $query;
    $resolveScopeFilter->handle($query, function (object $msg) use (&$captured): string {
        $captured = $msg;

        return 'ok';
    });

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

    $result = dispatchScopeQuery($resolveScopeFilter, $query);
    assert($result instanceof ResolveScopeFilterTestQuery);

    expect($result)->toBe($query);
    expect($result->accessContext())->toBeNull();
});

it('passes through ScopeAwareQuery without RequiresPermission', function (): void {
    $resolveScopeFilter = buildScopeMiddleware();
    $query = new ResolveScopeFilterTestNoPermissionQuery;

    $result = dispatchScopeQuery($resolveScopeFilter, $query);
    assert($result instanceof ResolveScopeFilterTestNoPermissionQuery);

    expect($result)->toBe($query);
    expect($result->accessContext())->toBeNull();
});

it('resolves All scope with null visibleIds for User target', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(scope: 'all');
    $result = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestQuery);
    assert($result instanceof ResolveScopeFilterTestQuery);

    $context = $result->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::All);
    expect($context->visibleIds)->toBeNull();
});

it('resolves Own scope with only current user id for User target', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(userId: 'user-42', scope: 'own');
    $result = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestQuery);
    assert($result instanceof ResolveScopeFilterTestQuery);

    $context = $result->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::Own);
    expect($context->visibleIds)->toBe(['user-42']);
});

it('resolves Team scope with visible user ids for User target', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(
        userId: 'user-1',
        scope: 'team',
        teamVisibleUserIds: ['user-1', 'user-2', 'user-3'],
    );
    $result = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestQuery);
    assert($result instanceof ResolveScopeFilterTestQuery);

    $context = $result->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::Team);
    expect($context->visibleIds)->toBe(['user-1', 'user-2', 'user-3']);
});

it('resolves All scope with null visibleIds for Team target', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(scope: 'all');
    $result = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestTeamQuery);
    assert($result instanceof ResolveScopeFilterTestTeamQuery);

    $context = $result->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::All);
    expect($context->visibleIds)->toBeNull();
});

it('resolves Own scope with empty array for Team target', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(userId: 'user-42', scope: 'own');
    $result = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestTeamQuery);
    assert($result instanceof ResolveScopeFilterTestTeamQuery);

    $context = $result->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::Own);
    expect($context->visibleIds)->toBe([]);
});

it('resolves Team scope with member team ids for Team target', function (): void {
    $resolveScopeFilter = buildScopeMiddleware(
        userId: 'user-1',
        scope: 'team',
        memberTeamIds: ['team-1', 'team-2'],
    );
    $result = dispatchScopeQuery($resolveScopeFilter, new ResolveScopeFilterTestTeamQuery);
    assert($result instanceof ResolveScopeFilterTestTeamQuery);

    $context = $result->accessContext();
    assert($context instanceof AccessContext);

    expect($context->scope)->toBe(AccessScope::Team);
    expect($context->visibleIds)->toBe(['team-1', 'team-2']);
});

/** @implements Query<list<string>> */
#[RequiresPermission('test.resource.read')]
final readonly class ResolveScopeFilterTestQuery implements Query, ScopeAwareQuery
{
    public function __construct(
        private ?AccessContext $accessContext = null,
    ) {}

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::User;
    }

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

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::User;
    }

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
#[RequiresPermission('test.team.read')]
final readonly class ResolveScopeFilterTestTeamQuery implements Query, ScopeAwareQuery
{
    public function __construct(
        private ?AccessContext $accessContext = null,
    ) {}

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::Team;
    }

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }
}
