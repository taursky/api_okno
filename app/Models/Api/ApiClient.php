<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class ApiClient extends Model
{
    use HasApiTokens;

    protected $table = 'api_clients';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function requestLogs(): HasMany
    {
        return $this->hasMany(
            ApiRequestLog::class,
            'api_client_id'
        );
    }
}
