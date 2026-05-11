<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Super Admin Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials are used by the SwiftFieldSeeder to create the initial
    | super admin user for the platform.
    |
    */
    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
        'email' => env('SUPER_ADMIN_EMAIL', 'admin@swiftfield.com'),
        'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
    ],
];
