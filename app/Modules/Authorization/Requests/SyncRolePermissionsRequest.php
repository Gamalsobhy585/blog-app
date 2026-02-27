<?php

namespace App\Modules\Authorization\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncRolePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // admin middleware already
    }

    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}