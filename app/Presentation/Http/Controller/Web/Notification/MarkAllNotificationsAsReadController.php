<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Notification;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Domain\Notification\Contract\Command\MarkAllNotificationsAsReadCommand;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[SkipPermissionCheck(reason: 'Users mark only their own notifications')]
final readonly class MarkAllNotificationsAsReadController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(): JsonResponse
    {
        $this->commandBus->dispatch(new MarkAllNotificationsAsReadCommand(
            userId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
