<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Service;

/**
 * Domain service contract for json schema validator in the Registry bounded context.
 */
interface JsonSchemaValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $schema
     * @return list<string>
     *                      Contract operation `validate`; see infrastructure for behavior.
     */
    public function validate(array $data, array $schema): array;
}
