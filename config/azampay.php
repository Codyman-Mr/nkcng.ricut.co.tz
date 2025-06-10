<?php

/**
 * Config for Azampay
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Azampay Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application as provided on Azampay.
    |
    */
    'appName' => env('AZAMPAY_APP_NAME', 'nkcng'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Client ID
    |--------------------------------------------------------------------------
    |
    | This value is the client ID of your application as provided on Azampay.
    |
    */
    'clientId' => env('AZAMPAY_CLIENT_ID', '28acd4c4-5011-401c-ac35-17b21c98ccaf'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Client Secret
    |--------------------------------------------------------------------------
    |
    | This value is the client secret of your application as provided on Azampay.
    |
    */
    'clientSecret' => env('AZAMPAY_CLIENT_SECRET', 'ByeqcxZIHX72d3Gfz1RwnJ4+eXTeXmvPjuhiKc4eRn88KCODQKL+byTeB+H9ZAa1UyMGo2LNKApfMlopg687Y+higp4P1vyLoCpVHScuQaI2MsoVp0BoeDm+6IPyb6Rp81Auns3nhp2kf17WFLrDVT9HpdmXxyHAezLbfNM5au5c4yPVyEkAzIENSGsvW5YbT0N0Uak3UGeUq8T7vuEmon80B+JT/dmhYBUyWtDHpZApAnRRCbUW+b+40QpnWlAJkhF8mVPwrRqgQh7gMPdA2kmVKUIoI6oqIMXnBEXpWcDmMWpYsBMfHigXZk+rm0qOCJf26WfaJm0ooSBRqq9XYAvM/UfHptL1ncGtUoRhzkxgETFhUfUIke0btoG6mtB6OpPh/Fdh//NKGRNqh8DCKCsCs8LFdHEz82uX0CQxb6Js+RGdsPCSw87R0TrPvv1KLmWMNRnFZ1+PRF44wV8AgGdq5NjByhUb7pg3gsSHAGR9XSZ9auFKq52mEb8Yhnd0Gnv5iLNc2sCj46yDQIN0EBr9ze/10/JyZW5Bep/1/nYX8+DiqMGz4sCyt5f+vJMz1oFsw4J2CmbPhFwRVrjV3nRjTg0jPsECbyIE73uEjCQPP0BR/XsVrX2/BV0Asx+Xt9Fd0qaOyeSSFWvXj1EyVbQ7tbfbLSrxMOk8oVLy0hM=
'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Environment
    |--------------------------------------------------------------------------
    |
    | This value is the environment of your application as registered on Azampay.
    | Available options are: sandbox, production/live
    */
    'environment' => env('AZAMPAY_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Token
    |--------------------------------------------------------------------------
    |
    | This value is the token of your application as registered on Azampay.
    | It is secure API token for your application to receive callback of
    | the payment from Azampay Payment API. You need to validate
    | this while receiving callback from Azampay Payment API.
    */
    'token' => env('AZAMPAY_TOKEN', '31468815-1c11-4af0-9e49-6e94b6b74c65'),
];
