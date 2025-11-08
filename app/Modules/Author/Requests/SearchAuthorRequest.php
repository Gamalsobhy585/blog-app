<?php

// SearchAuthorRequest.php
namespace App\Modules\Author\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'approved' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string', 'in:name,created_at,nationality'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}
