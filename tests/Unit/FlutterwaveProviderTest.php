<?php

use App\Services\Payment\FlutterwaveProvider;
use App\DTOs\PaymentRequest;
use App\DTOs\RefundRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

beforeEach(function () {
    config([
        'services.flutterwave.secret_key' => 'test_secret_key',
        'services.flutterwave.public_key' => 'test_public_key',
        'services.flutterwave.encryption_key' => 'test_encryption_key',
    ]);

    $this->provider = new FlutterwaveProvider();
});

test('can get provider name', function () {
    expect($this->provider->getName())->toBe('flutterwave');
});

test('can get public key', function () {
    expect($this->provider->getPublicKey())->toBe('test_public_key');
});

test('can initialize payment successfully', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'success',
            'data' => [
                'link' => 'https://checkout.flutterwave.com/pay/test-link',
            ],
        ]),
    ]);

    $paymentRequest = new PaymentRequest(
        amount: 50000,
        email: 'test@example.com',
        reference: 'TEST-REF-123',
        callbackUrl: 'https://example.com/callback',
        metadata: ['booking_id' => 1]
    );

    $response = $this->provider->initializePayment($paymentRequest);

    expect($response->success)->toBeTrue()
        ->and($response->authorizationUrl)->toBe('https://checkout.flutterwave.com/pay/test-link')
        ->and($response->reference)->toBe('TEST-REF-123')
        ->and($response->message)->toBe('Payment initialized successfully');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.flutterwave.com/v3/payments'
            && $request['tx_ref'] === 'TEST-REF-123'
            && $request['amount'] === 50000
            && $request['currency'] === 'NGN'
            && $request['customer']['email'] === 'test@example.com';
    });
});

test('handles payment initialization failure', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'error',
            'message' => 'Invalid API key',
        ], 401),
    ]);

    $paymentRequest = new PaymentRequest(
        amount: 50000,
        email: 'test@example.com',
        reference: 'TEST-REF-123',
        callbackUrl: 'https://example.com/callback',
        metadata: []
    );

    $response = $this->provider->initializePayment($paymentRequest);

    expect($response->success)->toBeFalse()
        ->and($response->message)->toContain('Invalid API key');
});

test('handles payment initialization exception', function () {
    Http::fake(function () {
        throw new \Exception('Network error');
    });

    $paymentRequest = new PaymentRequest(
        amount: 50000,
        email: 'test@example.com',
        reference: 'TEST-REF-123',
        callbackUrl: 'https://example.com/callback',
        metadata: []
    );

    $response = $this->provider->initializePayment($paymentRequest);

    expect($response->success)->toBeFalse()
        ->and($response->message)->toBe('An error occurred while initializing payment');
});

test('can verify payment successfully', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'success',
            'data' => [
                'status' => 'successful',
                'amount' => 50000,
                'tx_ref' => 'TEST-REF-123',
                'meta' => ['booking_id' => 1],
            ],
        ]),
    ]);

    $response = $this->provider->verifyPayment('TEST-REF-123');

    expect($response->success)->toBeTrue()
        ->and($response->status)->toBe('successful')
        ->and($response->amount)->toBe(50000.0)
        ->and($response->reference)->toBe('TEST-REF-123')
        ->and($response->metadata)->toBe(['booking_id' => 1])
        ->and($response->message)->toBe('Payment verified successfully');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/transactions/verify_by_reference')
            && $request['tx_ref'] === 'TEST-REF-123';
    });
});

test('handles payment verification failure', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'error',
            'message' => 'Transaction not found',
        ], 404),
    ]);

    $response = $this->provider->verifyPayment('INVALID-REF');

    expect($response->success)->toBeFalse()
        ->and($response->status)->toBe('failed')
        ->and($response->amount)->toBe(0.0)
        ->and($response->message)->toContain('Transaction not found');
});

test('handles payment verification exception', function () {
    Http::fake(function () {
        throw new \Exception('API timeout');
    });

    $response = $this->provider->verifyPayment('TEST-REF-123');

    expect($response->success)->toBeFalse()
        ->and($response->status)->toBe('error')
        ->and($response->message)->toBe('An error occurred while verifying payment');
});

test('can validate webhook signature correctly', function () {
    $request = Request::create('/webhook', 'POST', [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'successful',
            'tx_ref' => 'TEST-REF-123',
        ],
    ]);

    $request->headers->set('verif-hash', 'test_secret_key');

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeTrue();
});

test('rejects webhook with invalid signature', function () {
    $request = Request::create('/webhook', 'POST', [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'successful',
            'tx_ref' => 'TEST-REF-123',
        ],
    ]);

    $request->headers->set('verif-hash', 'invalid_signature');

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeFalse();
});

test('processes successful charge webhook', function () {
    $request = Request::create('/webhook', 'POST', [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'successful',
            'tx_ref' => 'TEST-REF-123',
            'amount' => 50000,
        ],
    ]);

    $request->headers->set('verif-hash', 'test_secret_key');

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeTrue();
});

test('ignores failed charge webhook', function () {
    $request = Request::create('/webhook', 'POST', [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'failed',
            'tx_ref' => 'TEST-REF-123',
        ],
    ]);

    $request->headers->set('verif-hash', 'test_secret_key');

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeFalse();
});

test('ignores non-charge events', function () {
    $request = Request::create('/webhook', 'POST', [
        'event' => 'transfer.completed',
        'data' => [],
    ]);

    $request->headers->set('verif-hash', 'test_secret_key');

    $result = $this->provider->handleWebhook($request);

    expect($result)->toBeFalse();
});

test('can process refund successfully', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'success',
            'data' => [
                'id' => 'refund-123',
            ],
        ]),
    ]);

    $refundRequest = new RefundRequest(
        transactionId: 'trans-123',
        amount: 25000
    );

    $response = $this->provider->refund($refundRequest);

    expect($response->success)->toBeTrue()
        ->and($response->refundId)->toBe('refund-123')
        ->and($response->message)->toBe('Refund processed successfully');

    Http::assertSent(function ($request) use ($refundRequest) {
        return str_contains($request->url(), "/transactions/{$refundRequest->transactionId}/refund")
            && $request['amount'] === 25000;
    });
});

test('handles full refund when amount is null', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'success',
            'data' => [
                'id' => 'refund-123',
            ],
        ]),
    ]);

    $refundRequest = new RefundRequest(
        transactionId: 'trans-123',
        amount: null
    );

    $response = $this->provider->refund($refundRequest);

    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return !isset($request['amount']); // Full refund doesn't send amount
    });
});

test('handles refund failure', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'error',
            'message' => 'Insufficient balance',
        ], 400),
    ]);

    $refundRequest = new RefundRequest(
        transactionId: 'trans-123',
        amount: 25000
    );

    $response = $this->provider->refund($refundRequest);

    expect($response->success)->toBeFalse()
        ->and($response->message)->toContain('Insufficient balance');
});

test('handles refund exception', function () {
    Http::fake(function () {
        throw new \Exception('Network error');
    });

    $refundRequest = new RefundRequest(
        transactionId: 'trans-123',
        amount: 25000
    );

    $response = $this->provider->refund($refundRequest);

    expect($response->success)->toBeFalse()
        ->and($response->message)->toBe('An error occurred while processing refund');
});
