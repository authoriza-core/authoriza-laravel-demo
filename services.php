<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
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

    /*
    |--------------------------------------------------------------------------
    | OpenID Connect (Авториза)
    |--------------------------------------------------------------------------
    */
    'oidc' => [
        // Базовый URL для Discovery
        'base_url' => env('OIDC_ISSUER_URL', 'https://a-kalinin-authoriza-backend-stand-d37a.twc1.net/oidc'),

        // Client ID и Secret 
        'client_id' => env('OIDC_CLIENT_ID'),
        'client_secret' => env('OIDC_CLIENT_SECRET'),

        // Redirect URI 
        'redirect' => env('OIDC_REDIRECT_URI', 'http://127.0.0.1:8000/auth/callback'),

        // Запрашиваемые Scope
        'scopes' => ['openid', 'profile', 'email', 'offline_access'],

    ],

];