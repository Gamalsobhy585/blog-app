<?php

namespace App\Modules\Author\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportAuthorsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'file' => ['required','file','mimes:csv,txt','max:10240'], // 10MB
        ];
    }
}   