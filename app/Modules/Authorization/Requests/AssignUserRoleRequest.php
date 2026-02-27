<?php

namespace App\Modules\Authorization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}