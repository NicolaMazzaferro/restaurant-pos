<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrinterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'header' => $this->header,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
            'vat' => $this->vat,
            'printer_port' => $this->printer_port,
        ];
    }
}
