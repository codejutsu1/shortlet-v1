<?php

use App\Models\Booking;
use App\Models\User;

test('login endpoint is rate limited after 5 attempts', function () {
    for ($i = 0; $i < 6; $i++) {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
    }

    // 6th request should be rate limited
    $response->assertStatus(429); // Too Many Requests
});

test('booking endpoint is rate limited after 10 attempts', function () {
    $user = User::factory()->create();
    $property = \App\Models\Property::factory()->create(['status' => 'active']);

    for ($i = 0; $i < 11; $i++) {
        $response = $this->actingAs($user)->post('/bookings', [
            'property_id' => $property->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(8)->toDateString(),
            'guests' => 2,
        ]);
    }

    // 11th request should be rate limited
    $response->assertStatus(429);
});

test('booking requires authorization to view', function () {
    $booking = Booking::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->get(route('bookings.show', $booking));

    $response->assertForbidden();
});

test('booking requires authorization to cancel', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);
    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->post(route('bookings.cancel', $booking));

    $response->assertForbidden();
});

test('security headers are present in response', function () {
    $response = $this->get('/');

    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-XSS-Protection', '1; mode=block');
    $response->assertHeader('Content-Security-Policy');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});
