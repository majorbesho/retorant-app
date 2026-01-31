<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Evolution API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp Evolution API integration.
    | Each restaurant will have its own Evolution instance.
    |
    */

    'api_url' => env('EVOLUTION_API_URL', 'https://api.xpilotae.cloud'),

    'api_key' => env('EVOLUTION_API_KEY', '429683C4C977415CAAFCCE10F7D57E11'),

    'webhook_url' => env('EVOLUTION_WEBHOOK_URL', 'https://app.xpilotae.cloud/api/whatsapp/webhook'),

    /*
    |--------------------------------------------------------------------------
    | Instance Settings
    |--------------------------------------------------------------------------
    */

    'instance_prefix' => env('EVOLUTION_INSTANCE_PREFIX', 'restaurant_'),

    'qrcode_enabled' => true,

    'webhook_events' => [
        'MESSAGES_UPSERT',      // Incoming messages
        'CONNECTION_UPDATE',     // Connection status changes
        'QRCODE_UPDATED',       // QR code updates
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Settings
    |--------------------------------------------------------------------------
    */

    'message_delay' => 1200, // Delay in ms to appear human-like

    'link_preview' => true,

    /*
    |--------------------------------------------------------------------------
    | Timeouts & Retries
    |--------------------------------------------------------------------------
    */

    'timeout' => 30, // API request timeout in seconds

    'retry_attempts' => 3,

    'retry_delay' => 2000, // Delay between retries in ms
];
