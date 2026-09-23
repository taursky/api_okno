<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Builder;

class Carrier extends ExternalModel
{
    public $table = 'carrier';
    public $primaryKey = 'id';
    public $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive($query): Builder
    {
        return $query->where('active', 1);
    }
}
