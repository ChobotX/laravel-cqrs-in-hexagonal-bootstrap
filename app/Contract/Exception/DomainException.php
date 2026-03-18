<?php

declare(strict_types=1);

namespace App\Contract\Exception;

use App\Contract\Translation\Translator;

interface DomainException
{
    public function userMessage(Translator $translator): string;

    public function statusCode(): int;
}
