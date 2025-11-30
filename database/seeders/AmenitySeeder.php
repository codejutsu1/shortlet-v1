<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            // Basic Amenities
            ['name' => 'Wi-Fi', 'icon' => 'wifi', 'category' => 'basic'],
            ['name' => 'Air Conditioning', 'icon' => 'ac-unit', 'category' => 'basic'],
            ['name' => 'Heating', 'icon' => 'thermostat', 'category' => 'basic'],
            ['name' => 'TV', 'icon' => 'tv', 'category' => 'basic'],
            ['name' => 'Kitchen', 'icon' => 'kitchen', 'category' => 'basic'],
            ['name' => 'Washing Machine', 'icon' => 'local-laundry-service', 'category' => 'basic'],
            ['name' => 'Dryer', 'icon' => 'local-laundry-service', 'category' => 'basic'],
            ['name' => 'Iron', 'icon' => 'iron', 'category' => 'basic'],
            ['name' => 'Hair Dryer', 'icon' => 'dry', 'category' => 'basic'],
            ['name' => 'Generator', 'icon' => 'bolt', 'category' => 'basic'],

            // Facilities
            ['name' => 'Swimming Pool', 'icon' => 'pool', 'category' => 'facilities'],
            ['name' => 'Parking', 'icon' => 'local-parking', 'category' => 'facilities'],
            ['name' => 'Gym', 'icon' => 'fitness-center', 'category' => 'facilities'],
            ['name' => 'Elevator', 'icon' => 'elevator', 'category' => 'facilities'],
            ['name' => 'Balcony', 'icon' => 'balcony', 'category' => 'facilities'],
            ['name' => 'Terrace', 'icon' => 'deck', 'category' => 'facilities'],
            ['name' => 'Garden', 'icon' => 'yard', 'category' => 'facilities'],
            ['name' => 'BBQ Grill', 'icon' => 'outdoor-grill', 'category' => 'facilities'],
            ['name' => 'Hot Tub', 'icon' => 'hot-tub', 'category' => 'facilities'],

            // Safety Amenities
            ['name' => 'Smoke Alarm', 'icon' => 'smoke-detector', 'category' => 'safety'],
            ['name' => 'Carbon Monoxide Alarm', 'icon' => 'sensor-occupied', 'category' => 'safety'],
            ['name' => 'Fire Extinguisher', 'icon' => 'fire-extinguisher', 'category' => 'safety'],
            ['name' => 'First Aid Kit', 'icon' => 'medical-services', 'category' => 'safety'],
            ['name' => 'Security Cameras', 'icon' => 'videocam', 'category' => 'safety'],
            ['name' => '24/7 Security', 'icon' => 'security', 'category' => 'safety'],
            ['name' => 'Safe', 'icon' => 'lock', 'category' => 'safety'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['slug' => Str::slug($amenity['name'])],
                $amenity + ['slug' => Str::slug($amenity['name'])]
            );
        }
    }
}
