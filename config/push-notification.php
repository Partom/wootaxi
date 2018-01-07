<?php

return array(

    'IOSUser'     => array(
        'environment' => env('IOS_USER_ENV', 'production'),
        'certificate' => app_path().'/apns/user/WooCabsUser.pem',
        'passPhrase'  => env('IOS_USER_PUSH_PASS', 'hepto123$'),
        'service'     => 'apns'
    ),
    'IOSProvider' => array(
        'environment' => env('IOS_PROVIDER_ENV', 'production'),
        'certificate' => app_path().'/apns/provider/WooCabsProvider.pem',
        'passPhrase'  => env('IOS_PROVIDER_PUSH_PASS', 'hepto123$'),
        'service'     => 'apns'
    ),
    'AndroidUser' => array(
        'environment' => env('ANDROID_USER_ENV', 'development'),
        'apiKey'      => env('ANDROID_USER_PUSH_KEY', 'yourAPIKey'),
        'service'     => 'gcm'
    ),
    'AndroidProvider' => array(
        'environment' => env('ANDROID_PROVIDER_ENV', 'development'),
        'apiKey'      => env('ANDROID_PROVIDER_PUSH_KEY', 'yourAPIKey'),
        'service'     => 'gcm'
    )

);