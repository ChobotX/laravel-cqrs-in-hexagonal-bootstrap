<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Sharing;

use App\Presentation\Http\Request\FormRequest;

final class ShareEntityRequest extends FormRequest
{
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
