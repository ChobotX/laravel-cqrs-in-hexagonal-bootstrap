<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

enum FieldType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Number = 'number';
    case Boolean = 'boolean';
    case Date = 'date';
    case Email = 'email';
    case Reference = 'reference';
    case File = 'file';
    case Repeater = 'repeater';
    case Object = 'object';
}
