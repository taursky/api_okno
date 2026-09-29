<?php

namespace App\Services\Crm;

use InvalidArgumentException;

class CrmWebhookRequestBuilder
{
    public function url(string $eventType): string
    {
        $entity = explode('.', $eventType)[0] ?? null;

        if (!$entity) {
            throw new InvalidArgumentException(
                'Invalid CRM event type: ' . $eventType
            );
        }

        $path = config('services.crm.webhook_' . $entity . '_url');

        if (!$path) {
            throw new InvalidArgumentException(
                'CRM webhook URL is not configured for ' . $entity
            );
        }

        return rtrim(config('services.crm.webhook_url'), '/')
            . '/'
            . ltrim($path, '/');
    }

    public function headers(string $body): array
    {
        $timestamp = (string) time();

        $signature = hash_hmac(
            'sha256',
            $timestamp . '.' . $body,
            (string) config('services.crm.webhook_secret')
        );

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Okno-Timestamp' => $timestamp,
            'X-Okno-Signature' => 'sha256=' . $signature,
        ];
    }
}
