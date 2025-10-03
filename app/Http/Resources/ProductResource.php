<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'category'=> $this->whenLoaded('category', fn()=>[
                'id'=>$this->category?->id,'name'=>$this->category?->name
            ]),
            'price'=>$this->price,
            'stock'=>$this->stock,
            'created_at'=>$this->created_at,
        ];
    }
}
