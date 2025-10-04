<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:120','unique:categories,name'],
            'slug' => ['nullable','string','max:140','unique:categories,slug'],
            'is_active' => ['sometimes','boolean'],
            'sort_order' => ['sometimes','integer','min:0','max:9999'],
        ];
    }
}
