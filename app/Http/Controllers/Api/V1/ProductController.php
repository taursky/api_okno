<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProductIndexRequest;
use App\Http\Resources\Api\V1\Product\ProductListResource;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Services\Api\V1\ProductService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(
        ProductIndexRequest $request
    ): AnonymousResourceCollection {
        return ProductListResource::collection(
            $this->productService->paginate(
                $request->validated()
            )
        );
    }

    public function show(int $id): ProductResource
    {
        return new ProductResource(
            $this->productService->find($id)
        );
    }
}
