<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'successful', 'failed']);

        return [
            'booking_id' => Booking::factory(),
            'amount' => fake()->randomFloat(2, 20000, 500000),
            'payment_method' => fake()->randomElement(['paystack', 'flutterwave']),
            'payment_reference' => 'PAY_' . strtoupper(fake()->unique()->bothify('???########')),
            'status' => $status,
            'paid_at' => $status === 'successful' ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    /**
     * Indicate that the payment was successful.
     */
    public function successful(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'successful',
            'paid_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }
}
