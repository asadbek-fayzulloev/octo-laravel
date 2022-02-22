<?php
return [
    'test' => env('OCTO_TEST', true),
    // other options...
    'octo_shop_id' => env('OCTO_SHOP_ID', ''),
    'octo_secret' =>  env('OCTO_SECRET', ''),
    'locale' => app()->getLocale(),
    'table' => [
        'transactions' => env('OCTO_TRANSACTIONS_TABLE','octo_transactions'),
        'orders' => env('OCTO_ORDERS_TABLE','orders'),
        'users' => env('OCTO_USERS_TABLE','users'),

    ],
    'redirect_after_verify' => env('OCTO_REDIRECT_AFTER_VERIFY', 'www.google.com')
];
