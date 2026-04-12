<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\Sensitive;
use App\Contract\Command\Command;

/**
 * Command payload for reset password in the User bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Guest action for password recovery')]
final readonly class ResetPasswordCommand implements Command
{
    public function __construct(
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $email,
        #[Sensitive]
        /** Field `token` for this contract; see module docs for validation rules. */
        public string $token,
        #[Sensitive]
        /** Password material as defined by the command (plain or hashed per handler contract). */
        public string $rawPassword,
    ) {}
}
