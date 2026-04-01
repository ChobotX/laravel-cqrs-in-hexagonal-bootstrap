<?php

declare(strict_types=1);

namespace App\Domain\Label;

use App\Domain\Label\Contract\LabelId;

final readonly class Label
{
    public function __construct(
        public LabelId $id,
        public LabelNamespace $namespace,
        public LabelName $name,
    ) {}
}
