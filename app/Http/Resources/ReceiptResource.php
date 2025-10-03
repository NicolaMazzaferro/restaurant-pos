<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'total' => (float) $this->total,
            'payment_method' => $this->payment_method->value ?? $this->payment_method,
            'issued_at' => optional($this->issued_at)->toIso8601String(),
        ];
    }
}
