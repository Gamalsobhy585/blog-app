<?php

namespace App\Modules\Book\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportBooksRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'file' => ['required','file','mimes:csv,txt','max:10240'], // 10MB
        ];
    }
}   