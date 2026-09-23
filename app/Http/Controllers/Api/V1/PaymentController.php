<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Payment\PaymentResource;
use App\Models\External\PaymentSystems;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function payments(): AnonymousResourceCollection
    {
        $payments = PaymentSystems::query()
            ->where('active', 1)
            ->orderBy('id')
            ->get();

        return PaymentResource::collection(
            $payments
        );
    }
}
