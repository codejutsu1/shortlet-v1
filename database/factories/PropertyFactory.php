<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan', 'Kano', 'Enugu', 'Calabar'];
        $states = ['Lagos', 'FCT', 'Rivers', 'Oyo', 'Kano', 'Enugu', 'Cross River'];
        $city = fake()->randomElement($cities);
        $state = $states[array_search($city, $cities)];

        $bedrooms = fake()->numberBetween(1, 5);

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                "Luxury {$bedrooms}BR Apartment in {$city}",
                "Modern {$bedrooms} Bedroom Flat in {$city}",
                "Spacious {$bedrooms}BR Home in {$city}",
                "Cozy {$bedrooms} Bedroom Apartment - {$city}",
                "Beautiful {$bedrooms}BR Property in {$city}",
            ]),
            'description' => fake()->paragraphs(3, true),
            'address' => fake()->streetAddress() . ', ' . $city,
            'city' => $city,
            'state' => $state,
            'price_per_night' => fake()->randomFloat(2, 15000, 200000),
            'bedrooms' => $bedrooms,
            'bathrooms' => fake()->numberBetween(1, $bedrooms),
            'max_guests' => $bedrooms * 2,
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the property is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
