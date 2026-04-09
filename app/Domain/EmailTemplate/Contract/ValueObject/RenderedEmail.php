<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\ValueObject;

final readonly class RenderedEmail
{
    public function __construct(
        public string $subject,
        public string $htmlBody,
    ) {}
}
