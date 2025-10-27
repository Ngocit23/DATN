<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sanctum Authentication Guard
    |--------------------------------------------------------------------------
    |
    | This option controls the authentication guard Sanctum will use to
    | authenticate incoming requests. You may set this to one of the
    | available authentication guards defined in your application.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Expiration Time
    |--------------------------------------------------------------------------
    |
    | When issuing tokens, you may specify an expiration time for each
    | token. By default, Sanctum uses the value defined by the "api"
    | guard in the session configuration file.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | You may specify the middleware Sanctum should apply to the routes
    | it registers. You may modify this array to add any custom
    | middleware you wish to apply to your Sanctum protected routes.
    |
    */

    'middleware' => [
        'web',
    ],
];
