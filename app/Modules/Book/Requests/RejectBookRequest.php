<?php

namespace App\Modules\Book\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectBookRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reason' => ['required','string','max:500'],
        ];
    }
}