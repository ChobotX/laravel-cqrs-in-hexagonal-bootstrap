<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

interface SchemaField
{
    public function name(): string;

    public function label(): string;

    public function type(): FieldType;

    public function isRequired(): bool;
}
