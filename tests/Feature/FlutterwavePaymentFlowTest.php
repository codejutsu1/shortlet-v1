<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Set Flutterwave as the payment provider
    config(['payment.default' => 'flutterwave']);
    config([
        'services.flutterwave.secret_key' => 'test_secret_key',
        'services.flutterwave.public_key' => 'test_public_key',
        'services.flutterwave.encryption_key' => 'test_encryption_key',
    ]);

    $this->user = User::factory()->create();
    $this->property = Property::factory()->create([
        'price_per_night' => 50000,
        'status' => 'active',
    ]);
    $this->booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $this->property->id,
        'status' => 'pending',
        'total_price' => 150000,
        'check_in' => now()->addDays(1),
        'check_out' => now()->addDays(4),
        'guests' => 2,
    ]);
});

test('user can initialize flutterwave payment for their pending booking', function () {
    Http::fake([
        'api.flutterwave.com/v3/payments' => Http::response([
            'status' => 'success',
            'message' => 'Payment link created',
            'data' => [
                'link' => 'https://checkout.flutterwave.com/test123',
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('payments.initialize', $this->booking));

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toContain('checkout.flutterwave.com');

    // Verify payment record was created
    $payment = Payment::where('booking_id', $this->booking->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('pending')
        ->and($payment->payment_method)->toBe('flutterwave')
        ->and($payment->amount)->toBe($this->booking->total_price);

    // Verify correct API payload
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.flutterwave.com/v3/payments'
            && $request['currency'] === 'NGN'
            && isset($request['customer']['email'])
            && isset($request['tx_ref']);
    });
});

test('user cannot initialize flutterwave payment for booking they do not own', function () {
    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->post(route('payments.initialize', $this->booking));

    $response->assertForbidden();
});

test('user cannot initialize flutterwave payment for non-pending booking', function () {
    $this->booking->update(['status' => 'confirmed']);

    $response = $this->actingAs($this->user)
        ->post(route('payments.initialize', $this->booking));

    $response->assertRedirect();
    $response->assertSessionHasErrors();
});

test('successful flutterwave payment callback updates booking and payment status', function () {
    $payment = Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'flw-test-ref-123',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'flutterwave',
    ]);

    Http::fake([
        'api.flutterwave.com/v3/transactions/verify_by_reference*' => Http::response([
            'status' => 'success',
            'message' => 'Transaction verified successfully',
            'data' => [
                'status' => 'successful',
                'tx_ref' => 'flw-test-ref-123',
                'amount' => 150000, // Flutterwave uses actual amount, not kobo
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'user_id' => $this->user->id,
                ],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('payments.callback', ['reference' => 'flw-test-ref-123']));

    $response->assertRedirect(route('payments.success', $this->booking));

    // Verify payment was marked as successful
    $payment->refresh();
    expect($payment->status)->toBe('successful')
        ->and($payment->paid_at)->not->toBeNull();

    // Verify booking was confirmed
    $this->booking->refresh();
    expect($this->booking->status)->toBe('confirmed');
});

test('failed flutterwave payment callback redirects to failed page', function () {
    Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'flw-failed-ref-123',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'flutterwave',
    ]);

    Http::fake([
        'api.flutterwave.com/v3/transactions/verify_by_reference*' => Http::response([
            'status' => 'error',
            'message' => 'Transaction not found',
        ], 404),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('payments.callback', ['reference' => 'flw-failed-ref-123']));

    $response->assertRedirect(route('payments.failed'));

    // Verify booking still pending
    $this->booking->refresh();
    expect($this->booking->status)->toBe('pending');
});

test('flutterwave webhook with valid signature is processed successfully', function () {
    $payment = Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'flw-webhook-ref-123',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'flutterwave',
    ]);

    $payload = [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'successful',
            'tx_ref' => 'flw-webhook-ref-123',
            'amount' => 150000,
            'meta' => [
                'booking_id' => $this->booking->id,
                'user_id' => $this->user->id,
            ],
        ],
    ];

    // Flutterwave uses 'verif-hash' header with secret key
    $response = $this->withHeaders([
        'verif-hash' => config('services.flutterwave.secret_key'),
    ])->postJson(route('webhooks.flutterwave'), $payload);

    $response->assertOk();

    // Verify payment was updated
    $payment->refresh();
    expect($payment->status)->toBe('successful');

    // Verify booking was confirmed
    $this->booking->refresh();
    expect($this->booking->status)->toBe('confirmed');
});

test('flutterwave webhook with invalid signature is rejected', function () {
    $payload = [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'successful',
            'tx_ref' => 'flw-webhook-invalid',
        ],
    ];

    $response = $this->withHeaders([
        'verif-hash' => 'invalid-signature',
    ])->postJson(route('webhooks.flutterwave'), $payload);

    $response->assertStatus(400);
});

test('flutterwave webhook ignores failed transactions', function () {
    $payment = Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'flw-webhook-failed',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'flutterwave',
    ]);

    $payload = [
        'event' => 'charge.completed',
        'data' => [
            'status' => 'failed', // Failed transaction
            'tx_ref' => 'flw-webhook-failed',
            'amount' => 150000,
        ],
    ];

    $response = $this->withHeaders([
        'verif-hash' => config('services.flutterwave.secret_key'),
    ])->postJson(route('webhooks.flutterwave'), $payload);

    $response->assertOk(); // Webhook accepted but not processed

    // Verify payment status unchanged
    $payment->refresh();
    expect($payment->status)->toBe('pending');

    // Verify booking status unchanged
    $this->booking->refresh();
    expect($this->booking->status)->toBe('pending');
});

test('flutterwave webhook ignores non-charge events', function () {
    $payload = [
        'event' => 'transfer.completed', // Different event type
        'data' => [
            'status' => 'successful',
        ],
    ];

    $response = $this->withHeaders([
        'verif-hash' => config('services.flutterwave.secret_key'),
    ])->postJson(route('webhooks.flutterwave'), $payload);

    $response->assertOk();
});

test('flutterwave payment callback without reference redirects to failed page', function () {
    $response = $this->actingAs($this->user)
        ->get(route('payments.callback'));

    $response->assertRedirect(route('payments.failed'));
});

test('flutterwave amount is handled correctly without conversion', function () {
    Http::fake([
        'api.flutterwave.com/v3/payments' => Http::response([
            'status' => 'success',
            'data' => [
                'link' => 'https://checkout.flutterwave.com/test',
            ],
        ], 200),
    ]);

    $this->actingAs($this->user)
        ->post(route('payments.initialize', $this->booking));

    // Verify Flutterwave receives amount in Naira (not kobo like Paystack)
    Http::assertSent(function ($request) {
        return $request['amount'] === 150000 // Same as total_price
            && !($request['amount'] === 15000000); // Not in kobo
    });
});

test('flutterwave provider is used when configured', function () {
    Http::fake([
        'api.flutterwave.com/*' => Http::response([
            'status' => 'success',
            'data' => ['link' => 'https://checkout.flutterwave.com/test'],
        ]),
    ]);

    $this->actingAs($this->user)
        ->post(route('payments.initialize', $this->booking));

    // Verify Flutterwave API was called (not Paystack)
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'flutterwave.com');
    });

    Http::assertNotSent(function ($request) {
        return str_contains($request->url(), 'paystack.co');
    });
});
