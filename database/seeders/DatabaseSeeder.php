<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user for authentication testing
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Only seed sample data in local/development environment
        if (app()->environment(['local', 'development'])) {
            $this->call([
                AmenitySeeder::class,
                PropertySeeder::class,
                PropertyImageSeeder::class,
            ]);

            $this->command->info('Sample data seeded successfully!');
        }
    }
}
