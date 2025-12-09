<?php

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authenticated user can create booking for available property', function () {
    $property = Property::factory()->create([
        'price_per_night' => 10000,
        'max_guests' => 4,
        'status' => 'active'
    ]);

    $response = $this->actingAs($this->user)->post(route('bookings.store'), [
        'property_id' => $property->id,
        'check_in' => now()->addDays(5)->toDateString(),
        'check_out' => now()->addDays(8)->toDateString(),
        'guests' => 2
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('bookings', [
        'user_id' => $this->user->id,
        'property_id' => $property->id,
        'status' => 'pending',
        'guests' => 2
    ]);
});

test('booking calculates correct total price', function () {
    $property = Property::factory()->create([
        'price_per_night' => 10000,
        'status' => 'active'
    ]);

    $this->actingAs($this->user)->post(route('bookings.store'), [
        'property_id' => $property->id,
        'check_in' => now()->addDays(5)->toDateString(),
        'check_out' => now()->addDays(8)->toDateString(),  // 3 nights
        'guests' => 2
    ]);

    $booking = Booking::where('user_id', $this->user->id)->first();
    expect($booking->total_price)->toEqual(30000.00);  // 3 nights * 10000
});

test('user cannot book property with conflicting dates', function () {
    $property = Property::factory()->create(['status' => 'active']);

    // Create existing booking
    Booking::factory()->create([
        'property_id' => $property->id,
        'check_in' => now()->addDays(5),
        'check_out' => now()->addDays(8),
        'status' => 'confirmed'
    ]);

    // Try to book overlapping dates
    $response = $this->actingAs($this->user)->post(route('bookings.store'), [
        'property_id' => $property->id,
        'check_in' => now()->addDays(6)->toDateString(),
        'check_out' => now()->addDays(9)->toDateString(),
        'guests' => 2
    ]);

    $response->assertSessionHasErrors('dates');
});

test('user cannot exceed property max guests', function () {
    $property = Property::factory()->create([
        'max_guests' => 2,
        'status' => 'active'
    ]);

    $response = $this->actingAs($this->user)->post(route('bookings.store'), [
        'property_id' => $property->id,
        'check_in' => now()->addDays(5)->toDateString(),
        'check_out' => now()->addDays(8)->toDateString(),
        'guests' => 5  // Exceeds max
    ]);

    $response->assertSessionHasErrors('guests');
});

test('user can view their own bookings', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id
    ]);

    $response = $this->actingAs($this->user)->get(route('bookings.index'));

    $response->assertOk();
    $response->assertInertia(
        fn($assert) =>
        $assert->has('bookings', 1)
    );
});

test('user can cancel their pending booking', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pending'
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertRedirect(route('bookings.index'));
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled'
    ]);
});

test('user can cancel their confirmed booking', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'confirmed'
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertRedirect(route('bookings.index'));
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled'
    ]);
});

test('user cannot cancel completed booking', function () {
    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'completed'
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertSessionHasErrors();
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'completed'  // Still completed
    ]);
});

test('user cannot cancel another users booking', function () {
    $otherUser = User::factory()->create();
    $booking = Booking::factory()->create([
        'user_id' => $otherUser->id,
        'status' => 'pending'
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));

    $response->assertForbidden();
});

test('booking creation is logged', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('Booking created successfully', \Mockery::type('array'));

    $property = Property::factory()->create([
        'price_per_night' => 10000,
        'status' => 'active'
    ]);

    $this->actingAs($this->user)->post(route('bookings.store'), [
        'property_id' => $property->id,
        'check_in' => now()->addDays(5)->toDateString(),
        'check_out' => now()->addDays(8)->toDateString(),
        'guests' => 2
    ]);
});

test('booking cancellation is logged', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('Booking cancelled', \Mockery::type('array'));

    $booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'pending'
    ]);

    $this->actingAs($this->user)
        ->post(route('bookings.cancel', $booking));
});
