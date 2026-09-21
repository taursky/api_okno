<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderIndexRequest;
use App\Http\Resources\Api\V1\OrderListResource;
use App\Http\Resources\Api\V1\OrderResource;
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
}
