<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Entity;

use App\Domain\Label\Contract\ValueObject\LabelId;
use App\Domain\Label\ValueObject\LabelName;
use App\Domain\Label\ValueObject\LabelNamespace;

final readonly class Label
{
    public function __construct(
        public LabelId $id,
        public LabelNamespace $namespace,
        public LabelName $name,
    ) {}
}
