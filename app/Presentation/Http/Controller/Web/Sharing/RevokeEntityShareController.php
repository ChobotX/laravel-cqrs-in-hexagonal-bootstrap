<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sharing;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Authorization\Contract\Command\RevokeRecordShareCommand;
use App\Domain\Authorization\Contract\Query\GetSharesForResourceQuery;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Resource-type permission and grantor-or-update check enforced inline')]
final readonly class RevokeEntityShareController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(string $resourceType, string $resourceId, string $granteeUserId): JsonResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';
        abort_if($userId === '', HttpStatus::FORBIDDEN);
        abort_unless($this->authorizationChecker->supportsResourceSharing($resourceType), HttpStatus::BAD_REQUEST);

        /** @var list<RecordShare> $shares */
        $shares = $this->queryBus->dispatch(new GetSharesForResourceQuery($resourceType, $resourceId));
        $targetShare = null;
        foreach ($shares as $share) {
            if ($share->granteeUserId === $granteeUserId) {
                $targetShare = $share;
                break;
            }
        }

        abort_unless($targetShare instanceof RecordShare, HttpStatus::NOT_FOUND);

        $isGrantor = $targetShare->grantorUserId === $userId;
        $canUpdate = $this->authorizationChecker->canShareResource($userId, $resourceType);
        abort_unless($isGrantor || $canUpdate, HttpStatus::FORBIDDEN);

        $this->commandBus->dispatch(new RevokeRecordShareCommand(
            granteeUserId: $granteeUserId,
            resourceType: $resourceType,
            resourceId: $resourceId,
        ));

        return new JsonResponse(status: HttpStatus::NO_CONTENT);
    }
}
