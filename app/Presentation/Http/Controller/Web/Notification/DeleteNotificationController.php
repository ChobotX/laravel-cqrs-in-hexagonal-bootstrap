<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Notification;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
use App\Domain\Notification\Contract\Command\DeleteNotificationCommand;
use App\Presentation\Http\Request\Web\Notification\DeleteNotificationRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[SkipPermissionCheck(reason: 'Ownership enforced in handler')]
final readonly class DeleteNotificationController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(DeleteNotificationRequest $deleteNotificationRequest): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteNotificationCommand(
            notificationId: $deleteNotificationRequest->notificationId(),
            userId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
