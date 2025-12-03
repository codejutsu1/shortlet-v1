<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use App\Services\PaymentManager;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
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

test('user can initialize payment for their pending booking', function () {
    Http::fake([
        'api.paystack.co/transaction/initialize' => Http::response([
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/test123',
                'access_code' => 'test_access_code',
                'reference' => 'BK-' . $this->booking->id . '-' . time(),
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('payments.initialize', $this->booking));

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toContain('checkout.paystack.com');

    // Verify payment record was created
    $payment = Payment::where('booking_id', $this->booking->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('pending')
        ->and($payment->payment_method)->toBe('paystack')
        ->and($payment->amount)->toBe($this->booking->total_price);
});

test('user cannot initialize payment for booking they do not own', function () {
    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->post(route('payments.initialize', $this->booking));

    $response->assertForbidden();
});

test('user cannot initialize payment for non-pending booking', function () {
    $this->booking->update(['status' => 'confirmed']);

    $response = $this->actingAs($this->user)
        ->post(route('payments.initialize', $this->booking));

    $response->assertRedirect();
    $response->assertSessionHasErrors();
});

test('successful payment callback updates booking and payment status', function () {
    $payment = Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'test-reference-123',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'paystack',
    ]);

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'status' => 'success',
                'reference' => 'test-reference-123',
                'amount' => 15000000, // amount in kobo
                'metadata' => [
                    'booking_id' => $this->booking->id,
                    'user_id' => $this->user->id,
                ],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('payments.callback', ['reference' => 'test-reference-123']));

    $response->assertRedirect(route('payments.success', $this->booking));

    // Verify payment was marked as successful
    $payment->refresh();
    expect($payment->status)->toBe('successful')
        ->and($payment->paid_at)->not->toBeNull();

    // Verify booking was confirmed
    $this->booking->refresh();
    expect($this->booking->status)->toBe('confirmed');
});

test('failed payment callback redirects to failed page', function () {
    Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'test-reference-failed',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'paystack',
    ]);

    Http::fake([
        'api.paystack.co/transaction/verify/*' => Http::response([
            'status' => false,
            'message' => 'Transaction not found',
        ], 404),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('payments.callback', ['reference' => 'test-reference-failed']));

    $response->assertRedirect(route('payments.failed'));

    // Verify booking still pending
    $this->booking->refresh();
    expect($this->booking->status)->toBe('pending');
});

test('webhook with valid signature is processed successfully', function () {
    $payment = Payment::factory()->create([
        'booking_id' => $this->booking->id,
        'payment_reference' => 'webhook-reference-123',
        'status' => 'pending',
        'amount' => $this->booking->total_price,
        'payment_method' => 'paystack',
    ]);

    $payload = [
        'event' => 'charge.success',
        'data' => [
            'status' => 'successful',
            'reference' => 'webhook-reference-123',
            'amount' => 15000000,
            'metadata' => [
                'booking_id' => $this->booking->id,
                'user_id' => $this->user->id,
            ],
        ],
    ];

    $signature = hash_hmac('sha512', json_encode($payload), config('services.paystack.secret_key'));

    $response = $this->withHeaders([
        'x-paystack-signature' => $signature,
    ])->postJson(route('webhooks.paystack'), $payload);

    $response->assertOk();

    // Verify payment was updated
    $payment->refresh();
    expect($payment->status)->toBe('successful');

    // Verify booking was confirmed
    $this->booking->refresh();
    expect($this->booking->status)->toBe('confirmed');
});

test('webhook with invalid signature is rejected', function () {
    $payload = [
        'event' => 'charge.success',
        'data' => [
            'status' => 'successful',
            'reference' => 'webhook-reference-invalid',
        ],
    ];

    $response = $this->withHeaders([
        'x-paystack-signature' => 'invalid-signature',
    ])->postJson(route('webhooks.paystack'), $payload);

    $response->assertStatus(400);
});

test('payment callback without reference redirects to failed page', function () {
    $response = $this->actingAs($this->user)
        ->get(route('payments.callback'));

    $response->assertRedirect(route('payments.failed'));
});
