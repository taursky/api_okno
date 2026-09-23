<?php

namespace App\Models\External;

class OrderState extends ExternalModel
{
    protected $table = 'order_state';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public function ScopeActive($query)
    {
        return $query->where('active', 1);
    }
}
