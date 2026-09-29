<?php

namespace App\Models\External;

class CrmWebhookEvent extends ExternalModel
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_RETRY = 'retry';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED = 'failed';

    protected $table = 'crm_webhook_events';

    protected $guarded = [];

    protected $casts = [
        'attempts' => 'integer',
        'last_http_status' => 'integer',
        'queued_at' => 'datetime',
        'next_attempt_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
}
