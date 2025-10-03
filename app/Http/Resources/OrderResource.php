<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status->value ?? $this->status,
            'type' => $this->type->value ?? $this->type,
            'total' => (float) $this->total,
            'items' => OrderItemResource::collection($this->whenLoaded('items', $this->items)),
            'receipt' => new ReceiptResource($this->whenLoaded('receipt', $this->receipt)),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
