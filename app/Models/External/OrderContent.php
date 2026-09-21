<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderContent extends ExternalModel
{
    protected $table = 'orders_content';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_id',
            'id'
        );
    }
}
