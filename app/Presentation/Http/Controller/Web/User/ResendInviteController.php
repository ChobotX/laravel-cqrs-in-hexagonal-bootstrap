<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\User\Contract\Command\ResendUserInviteCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.list.update')]
final readonly class ResendInviteController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $userId): RedirectResponse
    {
        $this->commandBus->dispatch(new ResendUserInviteCommand(
            userId: $userId,
        ));

        return back()->with('success', __('messages.users.invite_resent'));
    }
}
