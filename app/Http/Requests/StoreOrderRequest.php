<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type' => ['required','in:in_store,takeaway'],
            'payment_method' => ['sometimes','nullable','in:cash,card,other'],

            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:products,id'],
            'items.*.quantity'   => ['required','integer','min:1'],
            // opzionale: prezzo imposto dal client
            'items.*.price'      => ['sometimes','numeric','min:0'],
        ];
    }
}
