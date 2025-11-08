<?php


namespace App\Modules\Author\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 1 || $this->user()->role === 2;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'nationality' => ['nullable', 'string', 'max:100'],
        ];
    }


}