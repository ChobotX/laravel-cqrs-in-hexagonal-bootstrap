<?php

declare(strict_types=1);

namespace App\Application\Bus;

use App\Contract\Attribute\Sensitive;
use BackedEnum;
use ReflectionClass;
use ReflectionProperty;
use UnitEnum;

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
                $result[$name] = self::toJsonSafe($reflectionProperty->getValue($object));
            }
        }

        return $result;
    }

    private static function toJsonSafe(mixed $value): mixed
    {
        if ($value === null || is_scalar($value)) {
            return $value;
        }

        if (is_array($value)) {
            return array_map(self::toJsonSafe(...), $value);
        }

        if (! is_object($value)) {
            return '[non-serializable]';
        }

        if (method_exists($value, '__toString')) {
            return (string) $value;
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        return $value::class;
    }
}
