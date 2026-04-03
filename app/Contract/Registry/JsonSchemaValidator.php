<?php

declare(strict_types=1);

namespace App\Contract\Registry;

interface JsonSchemaValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $schema
     * @return list<string>
     */
    public function validate(array $data, array $schema): array;
}
