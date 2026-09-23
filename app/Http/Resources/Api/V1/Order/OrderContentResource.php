<?php

namespace App\Http\Resources\Api\V1\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->getAttributes();
    }
}
