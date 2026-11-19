<?php

return [

    /*
    | Default VAT rate (%) for new offers. 25 % is the standard Croatian PDV rate.
    */
    'vat_rate' => (float) env('CRM_VAT_RATE', 25),

    'currency' => env('CRM_CURRENCY', 'EUR'),

    /*
    | Number of days a new offer stays valid by default.
    */
    'offer_validity_days' => (int) env('CRM_OFFER_VALIDITY_DAYS', 30),

    /*
    | Seller details printed on offer PDFs.
    */
    'seller' => [
        'name' => env('CRM_SELLER_NAME', 'Damir CRM Demo d.o.o.'),
        'address' => env('CRM_SELLER_ADDRESS', 'Ilica 1, 10000 Zagreb, Croatia'),
        'oib' => env('CRM_SELLER_OIB', '12345678903'),
        'email' => env('CRM_SELLER_EMAIL', 'sales@damircrm.test'),
        'iban' => env('CRM_SELLER_IBAN', 'HR1210010051863000160'),
    ],

    /*
    | Public demo mode: shows demo accounts on the login screen, protects the
    | demo accounts from being changed and allows the nightly demo:reset.
    */
    'demo' => [
        'enabled' => (bool) env('DEMO_MODE', false),
        'password' => env('DEMO_PASSWORD', 'demo1234'),
        'domain' => 'damircrm.test',
    ],

    'locales' => [
        'en' => 'English',
        'hr' => 'Hrvatski',
    ],

];
