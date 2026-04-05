<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class FileField implements SchemaField
{
    /** @param list<string>|null $allowedMimeTypes */
    public function __construct(
        private string $name,
        private string $label,
        private bool $required = false,
        public ?array $allowedMimeTypes = null,
        public ?int $maxSizeBytes = null,
        public ?string $fileNamespace = null,
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
        return FieldType::File;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
