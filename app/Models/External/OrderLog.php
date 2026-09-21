<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLog extends ExternalModel
{
    protected $table = 'order_logs';

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
