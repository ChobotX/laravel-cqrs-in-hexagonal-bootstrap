<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Label;

use App\Domain\Label\Label;
use App\Domain\Label\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;

final readonly class LabelMapper
{
    public function toDomain(LabelModel $labelModel): Label
    {
        return new Label(
            id: new LabelId($labelModel->id),
            namespace: new LabelNamespace($labelModel->namespace),
            name: new LabelName($labelModel->name),
        );
    }
}
