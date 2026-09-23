<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderIndexRequest;
use App\Http\Resources\Api\V1\Order\OrderListResource;
use App\Http\Resources\Api\V1\Order\OrderPaymentResource;
use App\Http\Resources\Api\V1\Order\OrderProductListResource;
use App\Http\Resources\Api\V1\Order\OrderProductResource;
use App\Http\Resources\Api\V1\Order\OrderResource;
use App\Http\Resources\Api\V1\Order\OrderShipmentsResource;
use App\Http\Resources\Api\V1\Order\OrderStateResource;
use App\Services\Api\V1\OrderService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(OrderIndexRequest $request): AnonymousResourceCollection
    {
        $orders = $this->orderService->paginate(
            $request->validated()
        );

        return OrderListResource::collection($orders);
    }

    public function show(int $id): OrderResource
    {
        return new OrderResource(
            $this->orderService->find($id)
        );
    }

    public function orderStatuses()
    {
        return OrderStateResource::collection($this->orderService->orderStatuses());
    }

    public function products()
    {
        return OrderProductListResource::collection($this->orderService->products());
    }

    public function product($id)
    {
        return new OrderProductResource($this->orderService->product($id));
    }
}
