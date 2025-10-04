<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // 'category' può essere {id} nella rotta
        $id = (int) $this->route('category') ?? (int) $this->route('id');

        return [
            'name' => ['sometimes','string','max:120', Rule::unique('categories','name')->ignore($id)],
            'slug' => ['sometimes','nullable','string','max:140', Rule::unique('categories','slug')->ignore($id)],
            'is_active'  => ['sometimes','boolean'],
            'sort_order' => ['sometimes','integer','min:0','max:9999'],
        ];
    }
}
