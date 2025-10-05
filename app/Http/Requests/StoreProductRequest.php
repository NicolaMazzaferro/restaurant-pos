<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest {

    public function authorize(): bool { 
        return true; 
    }

    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => "Il file caricato deve essere un'immagine.",
            'image.mimes' => "Formato non valido: consenti JPG, JPEG, PNG o WebP.",
            'image.max'   => "L'immagine non deve superare i 2 MB.",
        ];
    }
}
