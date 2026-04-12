<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Entity;

use App\Domain\Label\Contract\ValueObject\LabelId;
use App\Domain\Label\ValueObject\LabelName;
use App\Domain\Label\ValueObject\LabelNamespace;

/**
 * Immutable read-model snapshot of a Label returned from queries in the Label context.
 */
final readonly class Label
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public LabelId $id,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public LabelNamespace $namespace,
        /** Human-visible label or title. */
        public LabelName $name,
    ) {}
}
