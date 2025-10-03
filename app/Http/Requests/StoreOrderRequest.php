<?php

namespace App\Http\Requests;

class StoreOrderRequest extends FormRequest {

    public function authorize(): bool {
        return true; 
    }

    public function rules(): array {
        return [
            'type' => 'required|in:in_store,takeaway',
            'payment_method' => 'nullable|in:cash,card,other',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'nullable|numeric|min:0', // permette override price se necessario
        ];
    }
}
