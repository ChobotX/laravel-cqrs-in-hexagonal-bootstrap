<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Command;

use App\Contract\Attribute\Sensitive;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Attribute\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Command payload for send templated email in the EmailTemplate bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Dispatched internally by event handlers, not by user action')]
#[SkipTransaction(reason: 'No database writes besides email log, sends external email')]
final readonly class SendTemplatedEmailCommand implements Command
{
    /**
     * @param  array<string, string|null>  $variables
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
        #[Sensitive]
        /** Array for `variables`; see constructor PHPDoc for structural tags when present. */
        public array $variables,
    ) {}
}
