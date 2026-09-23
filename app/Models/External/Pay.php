<?php

namespace App\Models\External;

class Pay extends ExternalModel
{

    protected $fillable = ['customer_id', 'cart_id', 'store', 'response', 'sum', 'currency_id'];
    protected $table = 'pay';
    protected $statusList = ['succeeded'=>1,'canceled'=>3,'waited'=>0];
    protected $casts =[
        'is_customer_view'=>'bool'
    ];
}
