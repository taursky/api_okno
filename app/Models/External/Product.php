<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends ExternalModel
{
    protected $table = 'product';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'price' => 'float',
        'wholesale_price' => 'float',
        'weight' => 'float',

        'active' => 'boolean',
        'available_for_order' => 'boolean',
        'has_option' => 'boolean',
        'is_euro_brand' => 'boolean',
    ];

    public function langs(): HasMany
    {
        return $this->hasMany(
            ProductLang::class,
            'product_id',
            'id'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('product.active', 1)
            ->where('product.available_for_order', 1)
            ->where('product.price', '>', 0);
    }
}
