<?php

declare(strict_types=1);

namespace App\Contract\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Sensitive {}
