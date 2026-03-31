<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Notification;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Notification\Query\CountUnreadNotifications\CountUnreadNotificationsQuery;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Notifications are accessible to all authenticated users')]
final readonly class CountUnreadNotificationsController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(): JsonResponse
    {
        $count = $this->queryBus->dispatch(new CountUnreadNotificationsQuery(
            userId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse(['count' => $count]);
    }
}
