<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShippingParams extends ExternalModel
{
    protected $table = 'order_shipping_params';

    protected $guarded = [];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_id',
            'id'
        );
    }
}
