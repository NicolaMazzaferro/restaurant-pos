<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'category' => new CategoryResource($this->whenLoaded('category', $this->category)),
            'price'    => (float) $this->price,
            'stock'    => (int) $this->stock,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'image_url'   => $this->image_url, // URL pubblico pronto per il frontend
            // opzionale: 'image_path' se serve solo lato admin (meglio NON esporlo)
        ];
    }
}
