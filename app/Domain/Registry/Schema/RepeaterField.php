<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class RepeaterField implements SchemaField
{
    /**
     * @param  list<SchemaField>  $fields
     */
    public function __construct(
        private string $name,
        private string $label,
        private bool $required,
        public array $fields,
        public int $minItems = 0,
        public ?int $maxItems = null,
        public ?string $helpText = null,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function type(): FieldType
    {
        return FieldType::Repeater;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
