<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    protected $table = 'api_request_logs';

    public $timestamps = false;

    protected $fillable = [
        'api_client_id',
        'method',
        'endpoint',
        'route_name',
        'request_ip',
        'user_agent',
        'request_data',
        'response_code',
        'response_data',
        'error_message',
        'execution_time_ms',
        'created_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'response_code' => 'integer',
        'execution_time_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            ApiClient::class,
            'api_client_id'
        );
    }
}
