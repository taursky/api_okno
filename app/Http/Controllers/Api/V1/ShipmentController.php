<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Shipment\ShipmentResource;
use App\Models\External\Carrier;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShipmentController extends Controller
{
    public function shipments(): AnonymousResourceCollection
    {
        $shipments = Carrier::query()
            ->active()
            ->orderBy('id')
            ->get();

        return ShipmentResource::collection(
            $shipments
        );
    }
}
