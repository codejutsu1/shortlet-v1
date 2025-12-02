<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls which payment provider will be used by default
    | for processing payments. You may set this to any of the providers
    | configured below.
    |
    | Supported: "paystack", "flutterwave"
    |
    */

    'default_provider' => env('PAYMENT_PROVIDER', 'paystack'),

    /*
    |--------------------------------------------------------------------------
    | Payment Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for all payment transactions.
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'NGN'),

    /*
    |--------------------------------------------------------------------------
    | Payment Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the payment providers for your application.
    |
    */

    'providers' => [
        'paystack' => [
            'name' => 'Paystack',
            'enabled' => true,
        ],
        'flutterwave' => [
            'name' => 'Flutterwave',
            'enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook URLs for each payment provider.
    |
    */

    'webhooks' => [
        'paystack' => env('PAYSTACK_WEBHOOK_URL', '/api/webhooks/paystack'),
        'flutterwave' => env('FLUTTERWAVE_WEBHOOK_URL', '/api/webhooks/flutterwave'),
    ],

];
