<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Authorization;

use App\Presentation\Http\Request\FormRequest;

use function array_column;
use function implode;

final class ManageUserPermissionsRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            '_action' => ['required', 'in:'.implode(',', array_column(UserPermissionAction::cases(), 'value'))],
            'role_id' => ['required_if:_action,assign_role,revoke_role', 'uuid'],
            'permission' => ['required_if:_action,set_override,remove_override', 'string'],
            'type' => ['required_if:_action,set_override', 'in:grant,deny'],
            'scope' => ['required_if:_action,set_override', 'in:all,team,own'],
        ];
    }

    public function action(): UserPermissionAction
    {
        return UserPermissionAction::from($this->string('_action')->toString());
    }
}
