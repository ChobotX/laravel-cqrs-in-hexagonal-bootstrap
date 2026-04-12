<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sharing;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Authorization\Contract\Query\GetSharesForResourceQuery;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUsersByIdsQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Dynamic resource-type permission enforced via AuthorizationChecker::canViewResourceShares')]
final readonly class GetEntitySharesController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(string $resourceType, string $resourceId): JsonResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';
        abort_if($userId === '', HttpStatus::FORBIDDEN);
        abort_unless($this->authorizationChecker->supportsResourceSharing($resourceType), HttpStatus::BAD_REQUEST);
        abort_unless($this->authorizationChecker->canViewResourceShares($userId, $resourceType), HttpStatus::FORBIDDEN);
        abort_unless($this->authorizationChecker->can($userId, 'users.list.read'), HttpStatus::FORBIDDEN);

        /** @var list<RecordShare> $shares */
        $shares = $this->queryBus->dispatch(new GetSharesForResourceQuery($resourceType, $resourceId));

        /** @var array<string, RecordShare> $byGrantee */
        $byGrantee = [];
        foreach ($shares as $share) {
            $byGrantee[$share->granteeUserId] ??= $share;
        }

        /** @var list<User> $users */
        $users = $this->queryBus->dispatch(new GetUsersByIdsQuery(array_keys($byGrantee)));

        /** @var array<string, User> $usersById */
        $usersById = [];
        foreach ($users as $user) {
            $usersById[$user->id->value] = $user;
        }

        $data = [];
        foreach ($byGrantee as $granteeUserId => $share) {
            $user = $usersById[$granteeUserId] ?? null;
            $data[] = [
                'grantee_user_id' => $granteeUserId,
                'grantee_name' => $user?->name->value,
                'grantee_email' => $user?->email->value,
                'grantor_user_id' => $share->grantorUserId,
            ];
        }

        return new JsonResponse(['data' => $data]);
    }
}
