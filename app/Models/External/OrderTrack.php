<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTrack extends ExternalModel
{
    protected $table = 'orders_track';

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
