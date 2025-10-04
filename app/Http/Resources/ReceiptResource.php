<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Trasforma la Receipt in un payload JSON consistente.
 */
class ReceiptResource extends JsonResource
{
    public function toArray($request): array
    {
        $order = $this->order;

        $issuedAtIso = null;
        if ($this->issued_at) {
            $issuedAtIso = $this->issued_at instanceof \Illuminate\Support\Carbon
                ? $this->issued_at->toIso8601String()
                : Carbon::parse($this->issued_at)->toIso8601String();
        }

        return [
            'id'             => $this->id,
            'order_id'       => $this->order_id,
            'total'          => (float) $this->total,
            'payment_method' => $this->payment_method instanceof \BackedEnum
                ? $this->payment_method->value
                : (string) $this->payment_method,
            'issued_at'      => $issuedAtIso,

            'order' => [
                'id'     => optional($order)->id,
                'type'   => optional($order)->type,
                'status' => optional($order)->status,
                'items'  => $order
                    ? $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product'    => optional($item->product)->name,
                            'qty'        => (int) $item->quantity,
                            'price'      => (float) $item->price,
                            'subtotal'   => (float) $item->subtotal,
                        ];
                    })->toArray()
                    : [],
            ],
        ];
    }
}
