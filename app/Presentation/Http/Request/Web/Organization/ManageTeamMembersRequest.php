<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Organization;

use App\Presentation\Http\Request\FormRequest;

final class ManageTeamMembersRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            '_action' => ['required', 'in:add_member,remove_member'],
            'user_id' => ['required', 'uuid'],
        ];
    }

    public function action(): string
    {
        return $this->string('_action')->toString();
    }

    public function userId(): string
    {
        return $this->string('user_id')->toString();
    }
}
