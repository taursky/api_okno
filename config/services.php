<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'crm' => [
        'webhook_url' => env('CRM_WEBHOOK_URL'),
        'webhook_order_url' => env('CRM_WEBHOOK_ORDERS_URL'),
        'webhook_secret' => env('CRM_WEBHOOK_SECRET'),
        'webhook_queue' => env('CRM_WEBHOOK_QUEUE', 'crm-webhooks'),
        'currency' => env('CRM_ORDER_CURRENCY', 'RUB'),
        'connect_timeout' => (int) env('CRM_WEBHOOK_CONNECT_TIMEOUT', 3),
        'queued_stale_minutes' => (int) env('CRM_WEBHOOK_QUEUED_STALE_MINUTES', 15),
        'processing_stale_minutes' => 15,
        'timeout' => (int) env('CRM_WEBHOOK_TIMEOUT', 5),
    ],

];
