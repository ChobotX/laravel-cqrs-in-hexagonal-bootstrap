<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\User\Contract\Command\AdminResetUserTwoFactorCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('user_recovery.two_factor.update')]
final readonly class ResetUserTwoFactorController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $userId): RedirectResponse
    {
        $this->commandBus->dispatch(new AdminResetUserTwoFactorCommand($userId));

        return back()->with('success', __('messages.users.two_factor_reset'));
    }
}
