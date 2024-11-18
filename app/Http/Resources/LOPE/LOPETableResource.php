<?php

namespace App\Http\Resources\LOPE;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LOPETableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'key_personnel' => $this->key_personnel,
            'quantity' => $this->quantity
        ];
    }
}
