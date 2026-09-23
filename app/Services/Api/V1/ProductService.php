<?php

namespace App\Services\Api\V1;

use App\Models\External\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

class ProductService
{
    public function paginate(array $filters): Paginator
    {
        $query = Product::query()
            ->with('langs');

        if (! empty($filters['id'])) {
            $query->whereKey(
                (int) $filters['id']
            );
        }

        if (! empty($filters['ids'])) {
            $ids = collect(
                explode(',', $filters['ids'])
            )
                ->map(fn ($id) => (int) trim($id))
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($ids !== []) {
                $query->whereIn('id', $ids);
            }
        }

        if (! empty($filters['reference'])) {
            $query->where(
                'reference',
                $filters['reference']
            );
        }

        if (isset($filters['active'])) {
            $query->where(
                'active',
                (bool) $filters['active']
            );
        }

        if (isset($filters['available_for_order'])) {
            $query->where(
                'available_for_order',
                (bool) $filters['available_for_order']
            );
        }

        if (! empty($filters['manufacturer_id'])) {
            $query->where(
                'manufacturer_id',
                (int) $filters['manufacturer_id']
            );
        }

        if (! empty($filters['name'])) {
            $name = $filters['name'];

            $query->whereHas(
                'langs',
                function ($query) use ($name, $filters) {
                    $query->where(
                        'name',
                        'like',
                        '%' . $name . '%'
                    );

                    if (! empty($filters['lang'])) {
                        $query->where(
                            'lang',
                            $filters['lang']
                        );
                    }
                }
            );
        }

        if (! empty($filters['created_from'])) {
            $query->where(
                'created_at',
                '>=',
                Carbon::parse(
                    $filters['created_from']
                )->startOfDay()
            );
        }

        if (! empty($filters['created_to'])) {
            $query->where(
                'created_at',
                '<=',
                Carbon::parse(
                    $filters['created_to']
                )->endOfDay()
            );
        }

        if (! empty($filters['updated_from'])) {
            $query->where(
                'updated_at',
                '>=',
                Carbon::parse(
                    $filters['updated_from']
                )->startOfDay()
            );
        }

        if (! empty($filters['updated_to'])) {
            $query->where(
                'updated_at',
                '<=',
                Carbon::parse(
                    $filters['updated_to']
                )->endOfDay()
            );
        }

        $sort = $filters['sort'] ?? 'updated_at';
        $direction = $filters['direction'] ?? 'desc';

        return $query
            ->orderBy($sort, $direction)
            ->orderBy('id', $direction)
            ->simplePaginate(
                perPage: (int) ($filters['per_page'] ?? 25)
            )
            ->withQueryString();
    }

    public function find(int $id): Product
    {
        return Product::query()
            ->with([
                'langs',
            ])
            ->findOrFail($id);
    }
}
