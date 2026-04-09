<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\ValueObject;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;

final readonly class EmailTemplateWithMetadata
{
    /**
     * @param  array<string, array{description: string, sensitive: bool, sample: string}>  $variables
     */
    public function __construct(
        public EmailTemplate $template,
        public string $typeName,
        public string $typeDescription,
        public array $variables,
    ) {}
}
