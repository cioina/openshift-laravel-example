<?php

//return [

//    /*
//    |--------------------------------------------------------------------------
//    | Laravel CORS Options
//    |--------------------------------------------------------------------------
//    |
//    | The allowed_methods and allowed_headers options are case-insensitive.
//    |
//    | You don't need to provide both allowed_origins and allowed_origins_patterns.
//    | If one of the strings passed matches, it is considered a valid origin.
//    |
//    | If array('*') is provided to allowed_methods, allowed_origins or allowed_headers
//    | all methods / origins / headers are allowed.
//    | https://github.com/fruitcake/laravel-cors/blob/master/config/cors.php
//       */

//    /*
//        * You can enable CORS for 1 or multiple paths.
//        * Example: ['api/*']
//        */
//    'paths' => ['api/*',],

//    'allowed_methods' => [
//            'POST',
//            'GET',
//            'OPTIONS',
//            'PUT',
//            'PATCH',
//            'DELETE',
//        ],

//    'allowed_headers' => [
//            'Content-Type',
//            'X-Auth-Token',
//            'Origin',
//            'Authorization',
//        ],

//    'exposed_headers' => [
//            'Cache-Control',
//            'Content-Language',
//            'Content-Type',
//            'Expires',
//            'Last-Modified',
//            'Pragma',
//        ],

//    /*
//        * Matches the request origin. `[*]` allows all origins.
//        */
//    'allowed_origins' => ['*'],

//    /*
//        * Matches the request origin with, similar to `Request::is()`
//        */
//    'allowed_origins_patterns' => [],


//    /*
//        * Sets the Access-Control-Max-Age response header when > 0.
//        */
//    'max_age' => 60 * 60 * 24,

//    /*
//        * Sets the Access-Control-Allow-Credentials header.
//        */
//    'supports_credentials' => false,
//];

return [

    /*
      * A cors profile determines which origins, methods, headers are allowed for
      * a given requests. The `DefaultProfile` reads its configuration from this
      * config file.
      *
      * You can easily create your own cors profile.
      * More info: https://github.com/spatie/laravel-cors/#creating-your-own-cors-profile
      */
    'cors_profile' => Spatie\Cors\CorsProfile\DefaultProfile::class,

    /*
      * This configuration is used by `DefaultProfile`.
      */
    'default_profile' => [

         //CORS requests normally don’t include cookies to prevent CSRF attacks. When set to true, the request can be made with/will include credentials such as Cookies.
         //The header should be omitted to imply false which means the CORS request will not be returned to the open tab. Cannot be used with wildcard
        'allow_credentials' => false,

        'allow_origins' => [
           ($GLOBALS['CIOINA_Config']->get('ForceSSL') ? $GLOBALS['CIOINA_Config']->get('APP_URL') : '*'),
        ],

        'allow_methods' => [
            'POST',
            'GET',
            'OPTIONS',
            'PUT',
            'PATCH',
            'DELETE',
        ],

        'allow_headers' => [
            'Content-Type',
            'X-Auth-Token',
            'Origin',
            'Authorization',
        ],

        'expose_headers' => [
            'Cache-Control',
            'Content-Language',
            'Content-Type',
            'Expires',
            'Last-Modified',
            'Pragma',
        ],

        'forbidden_response' => [
            'message' => 'Forbidden (cors).',
            'status' => 403,
        ],

        /*
          * Preflight request will respond with value for the max age header.
          */
          //https://www.moesif.com/blog/technical/cors/Authoritative-Guide-to-CORS-Cross-Origin-Resource-Sharing-for-REST-APIs/
          //Value in seconds to cache preflight request results (i.e the data in Access-Control-Allow-Headers and Access-Control-Allow-Methods headers).
          //Firefox maximum is 24 hrs and Chromium maximum is 10 minutes. Higher will have no effect
        'max_age' => 60 * 60 * 24,
    ],
];
