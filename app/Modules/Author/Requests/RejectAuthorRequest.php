<?php

namespace App\Modules\Author\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectAuthorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reason' => ['required','string','max:500'],
        ];
    }
}