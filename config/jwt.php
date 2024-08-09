<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to sign the JWT. You should set it to a random,
    | long, and secure string.
    |
    */

    'secret' => env('JWT_SECRET', 'your-secret-key'),

    /*
    |--------------------------------------------------------------------------
    | JWT Time To Live
    |--------------------------------------------------------------------------
    |
    | This value represents the time in minutes that the token will be
    | valid before it expires.
    |
    */

    'ttl' => env('JWT_TTL', 2628000),

];
