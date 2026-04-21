<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Sso;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

use function is_string;

final class SsoCallbackRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [];
    }

    /** @return array<string, scalar|array<int|string, mixed>|null> */
    public function payload(): array
    {
        /** @var array<string, scalar|array<int|string, mixed>|null> $all */
        $all = $this->all();

        return $all;
    }

    public function stateValue(): string
    {
        $value = $this->input('state');

        return is_string($value) ? $value : '';
    }
}
