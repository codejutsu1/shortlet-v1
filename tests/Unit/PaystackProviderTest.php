<?php

use App\DTOs\PaymentRequest;
use App\DTOs\RefundRequest;
use App\Services\Payment\PaystackProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.paystack.public_key' => 'pk_test_xxx',
        'services.paystack.secret_key' => 'sk_test_xxx',
    ]);

    $this->provider = new PaystackProvider();
});

test('provider returns correct name', function () {
    expect($this->provider->getName())->toBe('paystack');
});

test('provider returns public key', function () {
    expect($this->provider->getPublicKey())->toBe('pk_test_xxx');
});

test('initializePayment converts amount to kobo correctly', function () {
    Http::fake([
        'api.paystack.co/transaction/initialize' => Http::response([
            'status' => true,
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/test',
                'reference' => 'test-ref',
            ],
        ], 200),
    ]);

    $request = new PaymentRequest(
        amount: 1000.50,
        email: 'test@example.com',
        reference: 'test-ref',
        callbackUrl: 'http://example.com/callback',
        metadata: ['booking_id' => 1]
    );

    $response = $this->provider->initializePayment($request);

    Http::assertSent(function ($request) {
        $body = $request->data();
        // 1000.50 NGN = 100050 kobo
        return $body['amount'] === 100050;
    });

    expect($response->success)->toBeTrue()
        ->and($response->authorizationUrl)->toBe('https://checkout.paystack.com/test')
        ->and($response->reference)->toBe('test-ref');
});

test('initializePayment handles API errors gracefully', function () {
    Http::fake([
        'api.paystack.co/transaction/initialize' => Http::response([
            'status' => false,
            'message' => 'Invalid email address',
        ], 400),
    ]);

    $request = new PaymentRequest(
        amount: 1000,
        email: 'invalid-email',
        reference: 'test-ref',
        callbackUrl: 'http://example.com/callback'
    );

    $response = $this->provider->initializePayment($request);

    expect($response->success)->toBeFalse()
        ->and($response->message)->toContain('Invalid email');
});

test('verifyPayment converts amount from kobo correctly', function () {
    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'data' => [
                'status' => 'success',
                'reference' => 'test-ref',
                'amount' => 250000, // 2500 NGN in kobo
                'metadata' => ['booking_id' => 1],
            ],
        ], 200),
    ]);

    $response = $this->provider->verifyPayment('test-ref');

    expect($response->success)->toBeTrue()
        ->and($response->status)->toBe('success')
        ->and($response->amount)->toBe(2500.0) // Converted from kobo
        ->and($response->metadata)->toHaveKey('booking_id');
});

test('verifyPayment handles failed verification', function () {
    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => false,
            'message' => 'Transaction not found',
        ], 404),
    ]);

    $response = $this->provider->verifyPayment('invalid-ref');

    expect($response->success)->toBeFalse()
        ->and($response->status)->toBe('failed')
        ->and($response->amount)->toBe(0);
});

test('handleWebhook validates signature correctly', function () {
    $payload = [
        'event' => 'charge.success',
        'data' => [
            'reference' => 'test-ref',
            'status' => 'success',
        ],
    ];

    $body = json_encode($payload);
    $validSignature = hash_hmac('sha512', $body, 'sk_test_xxx');

    $request = Request::create('/webhook', 'POST', $payload, [], [], [
        'HTTP_X_PAYSTACK_SIGNATURE' => $validSignature,
    ], $body);

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeTrue();
});

test('handleWebhook rejects invalid signature', function () {
    $payload = [
        'event' => 'charge.success',
        'data' => [
            'reference' => 'test-ref',
        ],
    ];

    $request = Request::create('/webhook', 'POST', $payload, [], [], [
        'HTTP_X_PAYSTACK_SIGNATURE' => 'invalid-signature',
    ], json_encode($payload));

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeFalse();
});

test('handleWebhook only accepts charge.success events', function () {
    $payload = [
        'event' => 'charge.failed',
        'data' => [
            'reference' => 'test-ref',
        ],
    ];

    $body = json_encode($payload);
    $validSignature = hash_hmac('sha512', $body, 'sk_test_xxx');

    $request = Request::create('/webhook', 'POST', $payload, [], [], [
        'HTTP_X_PAYSTACK_SIGNATURE' => $validSignature,
    ], $body);

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeFalse();
});

test('refund converts amount to kobo when specified', function () {
    Http::fake([
        'api.paystack.co/refund' => Http::response([
            'status' => true,
            'data' => [
                'id' => 12345,
                'transaction' => 'test-transaction',
            ],
        ], 200),
    ]);

    $request = new RefundRequest(
        transactionId: 'test-transaction',
        amount: 500.75 // Partial refund
    );

    $response = $this->provider->refund($request);

    Http::assertSent(function ($request) {
        $body = $request->data();
        // 500.75 NGN = 50075 kobo
        return $body['amount'] === 50075;
    });

    expect($response->success)->toBeTrue()
        ->and($response->refundId)->toBe('12345');
});

test('refund processes full refund when amount not specified', function () {
    Http::fake([
        'api.paystack.co/refund' => Http::response([
            'status' => true,
            'data' => [
                'id' => 12345,
            ],
        ], 200),
    ]);

    $request = new RefundRequest(
        transactionId: 'test-transaction',
        amount: null // Full refund
    );

    $response = $this->provider->refund($request);

    Http::assertSent(function ($request) {
        $body = $request->data();
        // Amount should not be included for full refund
        return !isset($body['amount']);
    });

    expect($response->success)->toBeTrue();
});

test('refund handles API errors', function () {
    Http::fake([
        'api.paystack.co/refund' => Http::response([
            'status' => false,
            'message' => 'Transaction not found',
        ], 404),
    ]);

    $request = new RefundRequest(
        transactionId: 'invalid-transaction',
        amount: 1000
    );

    $response = $this->provider->refund($request);

    expect($response->success)->toBeFalse()
        ->and($response->message)->toContain('Transaction not found');
});
