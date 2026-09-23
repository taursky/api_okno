<?php

namespace App\Services\Api\V1;

use App\Models\External\Order;
use App\Models\External\OrderState;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Order::query()
            ->with([
                'orderState',
            ]);

        if (! empty($filters['created_from'])) {
            $query->where(
                'created_at',
                '>=',
                Carbon::createFromFormat(
                    'Y-m-d',
                    $filters['created_from']
                )->startOfDay()
            );
        }

        if (! empty($filters['created_to'])) {
            $query->where(
                'created_at',
                '<=',
                Carbon::createFromFormat(
                    'Y-m-d',
                    $filters['created_to']
                )->endOfDay()
            );
        }

        if (! empty($filters['updated_from'])) {
            $query->where(
                'updated_at',
                '>=',
                Carbon::createFromFormat(
                    'Y-m-d',
                    $filters['updated_from']
                )->startOfDay()
            );
        }

        if (! empty($filters['updated_to'])) {
            $query->where(
                'updated_at',
                '<=',
                Carbon::createFromFormat(
                    'Y-m-d',
                    $filters['updated_to']
                )->endOfDay()
            );
        }

        if (! empty($filters['status'])) {
            $query->where(
                'state',
                (int) $filters['status']
            );
        }

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(
                perPage: (int) ($filters['per_page'] ?? 25)
            )
            ->withQueryString();
    }

    public function find(int $id): Order
    {
        return Order::query()
            ->with([
                'orderState',
                'content',
                'shippingParams',
                'tracks',
                'stateLogs.state',
                'logs',
            ])
            ->findOrFail($id);
    }

    public function orderStatuses()
    {
        return OrderState::active()->get();
    }
}
