<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Registry\JsonSchemaValidator;

final readonly class FakeJsonSchemaValidator implements JsonSchemaValidator
{
    /** @param list<string> $errorsToReturn */
    public function __construct(private array $errorsToReturn = []) {}

    /** @return list<string> */
    public function validate(array $data, array $schema): array
    {
        return $this->errorsToReturn;
    }
}
