<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Notification;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\QueryBus;
use App\Domain\Notification\Contract\Query\CountUnreadNotificationsQuery;
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
