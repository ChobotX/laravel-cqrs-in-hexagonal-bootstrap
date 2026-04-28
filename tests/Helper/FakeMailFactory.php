<?php

declare(strict_types=1);

namespace Tests\Helper;

use Illuminate\Contracts\Mail\Factory;
use Illuminate\Contracts\Mail\Mailer;

final class FakeMailFactory implements Factory
{
    /** @var list<?string> */
    public array $resolvedNames = [];

    public function __construct(
        public FakeMailer $mailer = new FakeMailer,
    ) {}

    public function mailer($name = null): Mailer
    {
        $this->resolvedNames[] = $name;

        return $this->mailer;
    }
}
