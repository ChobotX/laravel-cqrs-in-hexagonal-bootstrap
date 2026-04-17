<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request;

use App\Presentation\Http\Request\Exception\InvalidRouteParameterException;

trait HandlesFormRequest
{
    public function routeString(string $param): string
    {
        $value = $this->route($param);

        if (! is_string($value)) {
            throw InvalidRouteParameterException::expectedString($param);
        }

        return $value;
    }
}
