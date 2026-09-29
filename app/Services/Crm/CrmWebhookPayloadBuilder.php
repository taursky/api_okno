<?php

namespace App\Services\Crm;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @deprecated
 */
class CrmWebhookPayloadBuilder
{
    public function make(string $eventType, string $entityId, ?string $updatedAt = null, array $extra = []): array
    {
        return  array_merge([
            'schema_version' => 1,
            'event_id' => (string) Str::uuid(),
            'event_type' => $eventType,
            'occurred_at' => Carbon::now()->toIso8601String(),
            'updated_at' => $updatedAt ?? Carbon::now()->toIso8601String(),
        ], $extra);
    }
}
