<?php

declare(strict_types=1);

namespace App\Contract\Http;

interface HttpStatus
{
    public const int BAD_REQUEST = 400;

    public const int FORBIDDEN = 403;

    public const int NOT_FOUND = 404;

    public const int CONFLICT = 409;

    public const int UNPROCESSABLE_ENTITY = 422;

    public const int CREATED = 201;

    public const int NO_CONTENT = 204;
}
