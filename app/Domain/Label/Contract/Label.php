<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract;

use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;

final readonly class Label
{
    public function __construct(
        public LabelId $id,
        public LabelNamespace $namespace,
        public LabelName $name,
    ) {}
}
