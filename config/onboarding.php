<?php

return [
    'email_provider' => env('ONBOARDING_EMAIL_PROVIDER', 'log'),

    'microsoft' => [
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'mail_from' => env('MICROSOFT_MAIL_FROM'),
    ],

    'document_storage_provider' => env('ONBOARDING_DOCUMENT_STORAGE_PROVIDER', 'local'),

    'sharepoint' => [
        'site_id' => env('MICROSOFT_SHAREPOINT_SITE_ID'),
        'drive_id' => env('MICROSOFT_SHAREPOINT_DRIVE_ID'),
        'folder' => env('MICROSOFT_SHAREPOINT_FOLDER', 'OnboardingFlow'),
    ],
];