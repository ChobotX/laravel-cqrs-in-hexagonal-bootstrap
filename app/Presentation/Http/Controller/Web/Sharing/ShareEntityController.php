<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sharing;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Authorization\Contract\Command\ShareRecordCommand;
use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\Sharing\ShareEntityRequest;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Dynamic resource-type permission enforced via AuthorizationChecker::canShareResource')]
final readonly class ShareEntityController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(ShareEntityRequest $shareEntityRequest, string $resourceType, string $resourceId): JsonResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';
        abort_if($userId === '', HttpStatus::FORBIDDEN);
        abort_unless($this->authorizationChecker->supportsResourceSharing($resourceType), HttpStatus::BAD_REQUEST);
        abort_unless($this->authorizationChecker->canShareResource($userId, $resourceType), HttpStatus::FORBIDDEN);
        abort_unless($this->authorizationChecker->can($userId, 'users.list.read'), HttpStatus::FORBIDDEN);

        $granteeUserId = $shareEntityRequest->granteeUserId();
        abort_if($granteeUserId === $userId, HttpStatus::BAD_REQUEST);

        $this->commandBus->dispatch(new ShareRecordCommand(
            granteeUserId: $granteeUserId,
            resourceType: $resourceType,
            resourceId: $resourceId,
            actions: [Action::Read->value, Action::Update->value],
            grantorUserId: $userId,
        ));

        return new JsonResponse([
            'data' => [
                'grantee_user_id' => $granteeUserId,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
            ],
        ], HttpStatus::CREATED);
    }
}
