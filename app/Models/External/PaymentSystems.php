<?php

namespace App\Models\External;


class PaymentSystems extends ExternalModel
{
    public $table = 'payment_systems';
    public $primaryKey = 'id';
    public $guarded = [];

}
