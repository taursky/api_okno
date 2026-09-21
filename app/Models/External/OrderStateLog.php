<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderStateLog extends ExternalModel
{
    protected $table = 'order_state_change_log';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function state(): HasOne
    {
        return $this->hasOne(
            OrderState::class,
            'id',
            'state_id'
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_id',
            'id'
        );
    }
}
