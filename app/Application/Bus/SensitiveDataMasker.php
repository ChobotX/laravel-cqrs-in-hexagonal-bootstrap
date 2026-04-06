<?php

declare(strict_types=1);

namespace App\Application\Bus;

use ReflectionClass;
use ReflectionProperty;

final readonly class SensitiveDataMasker
{
    private const string MASK = '***';

    /**
     * @return array<string, mixed>
     */
    public static function mask(object $object): array
    {
        $reflectionClass = new ReflectionClass($object);
        $result = [];

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $name = $reflectionProperty->getName();

            if ($reflectionProperty->getAttributes(Sensitive::class) !== []) {
                $result[$name] = self::MASK;
            } else {
                $result[$name] = $reflectionProperty->getValue($object);
            }
        }

        return $result;
    }
}
