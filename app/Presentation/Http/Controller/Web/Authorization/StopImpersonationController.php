<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Contract\Command\StopImpersonation\StopImpersonationCommand;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('Available to any impersonating user')]
final readonly class StopImpersonationController
{
    public function __construct(
        private CommandBus $commandBus,
        private Guard $guard,
    ) {}

    public function __invoke(): RedirectResponse
    {
        /** @var string $impersonatorId */
        $impersonatorId = $this->guard->id();

        $this->commandBus->dispatch(new StopImpersonationCommand(
            impersonatorId: $impersonatorId,
        ));

        return redirect()->back()->with('success', __('messages.impersonation.stopped'));
    }
}
