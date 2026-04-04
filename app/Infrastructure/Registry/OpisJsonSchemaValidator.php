<?php

declare(strict_types=1);

namespace App\Infrastructure\Registry;

use App\Domain\Registry\Contract\Service\JsonSchemaValidator;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use stdClass;

final readonly class OpisJsonSchemaValidator implements JsonSchemaValidator
{
    public function validate(array $data, array $schema): array
    {
        $validator = new Validator;

        /** @var stdClass $dataObject */
        $dataObject = json_decode(json_encode($data, JSON_THROW_ON_ERROR), false, JSON_THROW_ON_ERROR);
        /** @var stdClass $schemaObject */
        $schemaObject = json_decode(json_encode($schema, JSON_THROW_ON_ERROR), false, JSON_THROW_ON_ERROR);

        $validationResult = $validator->validate($dataObject, $schemaObject);

        if ($validationResult->isValid()) {
            return [];
        }

        $error = $validationResult->error();

        return $error instanceof ValidationError ? $this->formatErrors($error) : [];
    }

    /** @return list<string> */
    private function formatErrors(ValidationError $validationError): array
    {
        $errorFormatter = new ErrorFormatter;
        /** @var array<string, list<string>> $errors */
        $errors = $errorFormatter->format($validationError);

        $messages = [];

        foreach ($errors as $path => $pathErrors) {
            foreach ($pathErrors as $pathError) {
                $messages[] = $path !== '' ? sprintf('%s: %s', $path, $pathError) : $pathError;
            }
        }

        return $messages;
    }
}
