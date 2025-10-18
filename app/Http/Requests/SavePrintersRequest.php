<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePrintersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // in futuro potresti legare a ruoli (admin)
    }

    public function rules(): array
    {
        return [
            'printers' => ['required', 'array'],
            'printers.*.id' => ['required', 'uuid'],
            'printers.*.name' => ['required', 'string', 'max:255'],
            'printers.*.model' => ['nullable', 'string', 'max:255'],
            'printers.*.header' => ['nullable', 'string', 'max:255'],
            'printers.*.address' => ['nullable', 'string', 'max:255'],
            'printers.*.city' => ['nullable', 'string', 'max:255'],
            'printers.*.phone' => ['nullable', 'string', 'max:255'],
            'printers.*.vat' => ['nullable', 'string', 'max:255'],
            'printers.*.printer_port' => ['nullable', 'string', 'max:255'],
        ];
    }
}
