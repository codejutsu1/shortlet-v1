<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 day', '+3 months');
        $checkOut = fake()->dateTimeBetween($checkIn, '+2 weeks');
        $nights = $checkIn->diff($checkOut)->days;
        $pricePerNight = fake()->randomFloat(2, 20000, 150000);

        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => fake()->numberBetween(1, 6),
            'total_price' => $pricePerNight * max($nights, 1),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
        ];
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        $checkIn = fake()->dateTimeBetween('-2 months', '-1 month');
        $checkOut = fake()->dateTimeBetween($checkIn, '-1 week');

        return $this->state(fn(array $attributes) => [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
