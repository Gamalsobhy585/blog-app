<?php

namespace App\Modules\Book\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:books,slug'],
            'total_copies' => ['required', 'integer', 'min:0'],
            'available_copies' => ['required', 'integer', 'min:0', 'lte:total_copies'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'integer', 'in:0,1'],
            'author_id' => ['required', 'integer', 'exists:authors,id'],
            'cover_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}