<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order' => $this->resource->getAttributes(),

            'status' => $this->whenLoaded(
                'orderState',
                fn () => [
                    'id' => $this->orderState?->id,
                    'name' => $this->orderState?->name,
                    'name_for_client' => $this->orderState?->name_for_client,
                ]
            ),

            'content' => OrderContentResource::collection(
                $this->whenLoaded('content')
            ),

            'shipping_params' => $this->whenLoaded(
                'shippingParams',
                fn () => $this->shippingParams?->getAttributes()
            ),

            'tracks' => $this->whenLoaded(
                'tracks',
                fn () => $this->tracks
                    ->map(fn ($track) => $track->getAttributes())
                    ->values()
            ),

            'state_history' => $this->whenLoaded(
                'stateLogs',
                fn () => $this->stateLogs
                    ->map(function ($log) {
                        return [
                            ...$log->getAttributes(),

                            'state' => $log->relationLoaded('state')
                                ? [
                                    'id' => $log->state?->id,
                                    'name' => $log->state?->name,
                                    'name_for_client' => $log->state?->name_for_client,
                                ]
                                : null,
                        ];
                    })
                    ->values()
            ),

            'logs' => $this->whenLoaded(
                'logs',
                fn () => $this->logs
                    ->map(fn ($log) => $log->getAttributes())
                    ->values()
            ),
        ];
    }
}
