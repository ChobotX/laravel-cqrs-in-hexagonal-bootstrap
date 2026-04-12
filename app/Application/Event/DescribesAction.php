<?php

declare(strict_types=1);

namespace App\Application\Event;

use ReflectionClass;

use function preg_replace;

trait DescribesAction
{
    public function actionLabel(): string
    {
        $shortName = new ReflectionClass($this)->getShortName();

        return preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $shortName) ?? $shortName;
    }
}
