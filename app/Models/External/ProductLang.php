<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLang extends ExternalModel
{
    public $table = 'product_lang';
    public $primaryKey = 'id';
    public $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class,
            'product_id',
            'id'
        );
    }
}
