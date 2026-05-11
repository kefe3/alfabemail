<?php

return [
    'api_base_url'     => env('MAILCOW_API_BASE_URL', ''),
    'api_key'          => env('MAILCOW_API_KEY', ''),
    'domain'           => env('MAILCOW_DOMAIN', 'alfabe.co'),
    'default_quota_mb' => (int) env('MAILCOW_DEFAULT_QUOTA_MB', 1024),
];
