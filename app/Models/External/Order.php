<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends ExternalModel
{
    protected $table = 'orders';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function content(): HasMany
    {
        return $this->hasMany(
            OrderContent::class,
            'order_id',
            'id'
        );
    }

    public function orderState(): HasOne
    {
        return $this->hasOne(
            OrderState::class,
            'id',
            'state'
        );
    }

    public function shippingParams(): HasOne
    {
        return $this->hasOne(
            OrderShippingParams::class,
            'order_id',
            'id'
        );
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(
            OrderTrack::class,
            'order_id',
            'id'
        );
    }

    public function stateLogs(): HasMany
    {
        return $this->hasMany(
            OrderStateLog::class,
            'order_id',
            'id'
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(
            OrderLog::class,
            'order_id',
            'id'
        );
    }
}
