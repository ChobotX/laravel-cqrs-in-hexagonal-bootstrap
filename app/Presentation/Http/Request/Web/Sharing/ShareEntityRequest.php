<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Sharing;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class ShareEntityRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'grantee_user_id' => ['required', 'uuid'],
        ];
    }

    public function granteeUserId(): string
    {
        return $this->string('grantee_user_id')->toString();
    }
}
