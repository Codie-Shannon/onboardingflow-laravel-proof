<?php

return [
    'email_provider' => env('ONBOARDING_EMAIL_PROVIDER', 'log'),

    'microsoft' => [
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'mail_from' => env('MICROSOFT_MAIL_FROM'),
    ],
];