<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, Woowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id' => '1801640613463835',
        'client_secret' => 'e5b7f53b74eaa9db9751a44516b0843e',
        'redirect' => 'http://heptotechnologies.online/woo/auth/facebook/callback',
    ],

    'google' => [
        'client_id' => '93259422387-mgu2eiiodjdl4pab7gbv2nbesgq68n80.apps.googleusercontent.com',
        'client_secret' => 'MsQWHyINQRVOyUAScyAoQoL4',
        'redirect' => 'http://heptotechnologies.online/woo/auth/google/callback',
    ]

];
