<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'status' => [
                'id' => $this->state,
                'name' => $this->whenLoaded(
                    'orderState',
                    fn () => $this->orderState?->name_for_client
                        ?? $this->orderState?->name
                ),
            ],

            'user_id' => $this->user_id,

            'total_products' => $this->total_products,
            'total_shipping' => $this->total_shipping,
            'total_discounts' => $this->total_discounts,
            'total_paid' => $this->total_paid,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
