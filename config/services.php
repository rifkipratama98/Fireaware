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

    'openaq' => [
        'key' => env('OPENAQ_API_KEY'),
        'url' => env('OPENAQ_API_URL', 'https://api.openaq.org/v3'),
    ],

    'firms' => [
        'map_key' => env('FIRMS_MAP_KEY'),
        'url' => env('FIRMS_BASE_URL', 'https://firms.modaps.eosdis.nasa.gov/api/area/csv'),
        'source' => env('FIRMS_SOURCE', 'VIIRS_SNPP_NRT'),
        'area' => env('FIRMS_AREA', '108.5,-4.5,119.5,4.5'),
        'day_range' => (int) env('FIRMS_DAY_RANGE', 1),
    ],

    'bmkg' => [
        'karhutla_url' => env('BMKG_KARHUTLA_URL'),
        'warning_url' => env('BMKG_WARNING_URL'),
    ],

];
