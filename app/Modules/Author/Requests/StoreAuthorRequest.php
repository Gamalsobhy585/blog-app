<?php


namespace App\Modules\Author\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
         return in_array($this->user()->role, ['1', '2']); 
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