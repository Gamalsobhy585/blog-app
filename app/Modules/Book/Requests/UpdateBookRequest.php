<?php
// UpdateBookRequest.php
namespace App\Modules\Book\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // put book data update fields here 

        ];
    }


}