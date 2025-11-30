<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default property owner if none exists
        $owner = User::firstOrCreate(
            ['email' => 'owner@shortlet.ng'],
            [
                'name' => 'Property Owner',
                'password' => bcrypt('password'),
            ]
        );

        $properties = [
            [
                'title' => 'Luxury 3-Bedroom Apartment in Lekki Phase 1',
                'description' => 'Experience the height of luxury in this stunning 3-bedroom apartment located in the heart of Lekki Phase 1. This beautifully furnished apartment features modern amenities, a spacious living area, and a fully equipped kitchen. Perfect for families or business travelers seeking comfort and convenience.',
                'address' => '15 Admiralty Way, Lekki Phase 1',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'price_per_night' => 75000.00,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'max_guests' => 6,
                'status' => 'active',
            ],
            [
                'title' => 'Cozy Studio Apartment in Victoria Island',
                'description' => 'A charming studio apartment perfect for solo travelers or couples. Located in the bustling Victoria Island area, this apartment offers easy access to shopping centers, restaurants, and nightlife. Fully furnished with modern appliances and high-speed Wi-Fi.',
                'address' => '42 Akin Adesola Street, Victoria Island',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'price_per_night' => 35000.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'max_guests' => 2,
                'status' => 'active',
            ],
            [
                'title' => 'Spacious 4-Bedroom Duplex in Banana Island',
                'description' => 'Indulge in ultimate luxury with this magnificent 4-bedroom duplex in the prestigious Banana Island. This property boasts breathtaking views, a private pool, 24/7 security, and world-class amenities. Ideal for executives and high-profile guests.',
                'address' => '8 Ocean Parade Road, Banana Island',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'price_per_night' => 250000.00,
                'bedrooms' => 4,
                'bathrooms' => 5,
                'max_guests' => 8,
                'status' => 'active',
            ],
            [
                'title' => 'Modern 2-Bedroom Flat in Maitama, Abuja',
                'description' => 'A sleek and modern 2-bedroom apartment in the upscale Maitama district of Abuja. This property features contemporary decor, a well-equipped kitchen, and ample parking space. Close to embassies, corporate offices, and fine dining establishments.',
                'address' => '12 Aguiyi Ironsi Street, Maitama',
                'city' => 'Abuja',
                'state' => 'FCT',
                'price_per_night' => 60000.00,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'max_guests' => 4,
                'status' => 'active',
            ],
            [
                'title' => 'Budget-Friendly 1-Bedroom in Wuse 2, Abuja',
                'description' => 'An affordable and comfortable 1-bedroom apartment in the heart of Wuse 2. Perfect for business travelers on a budget, this property offers all essential amenities including Wi-Fi, air conditioning, and a workspace. Walking distance to shops and restaurants.',
                'address' => '23 Adetokunbo Ademola Crescent, Wuse 2',
                'city' => 'Abuja',
                'state' => 'FCT',
                'price_per_night' => 25000.00,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'max_guests' => 2,
                'status' => 'active',
            ],
            [
                'title' => 'Elegant 3-Bedroom Villa in Port Harcourt GRA',
                'description' => 'A beautiful 3-bedroom villa nestled in the serene Garden City GRA. This property features a lush garden, parking for multiple vehicles, and top-notch security. Ideal for families and corporate guests visiting Port Harcourt.',
                'address' => '5 Forces Avenue, GRA Phase 2',
                'city' => 'Port Harcourt',
                'state' => 'Rivers',
                'price_per_night' => 55000.00,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'max_guests' => 6,
                'status' => 'active',
            ],
            [
                'title' => 'Charming 2-Bedroom Apartment in Ibadan',
                'description' => 'A comfortable 2-bedroom apartment in the cultural city of Ibadan. This property offers a blend of traditional and modern aesthetics, with spacious rooms and a peaceful environment. Great for tourists exploring the historic landmarks of Ibadan.',
                'address' => '18 Bodija Estate, Ibadan',
                'city' => 'Ibadan',
                'state' => 'Oyo',
                'price_per_night' => 30000.00,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'max_guests' => 4,
                'status' => 'active',
            ],
            [
                'title' => 'Luxury Penthouse in Ikoyi',
                'description' => 'Experience unparalleled luxury in this stunning penthouse in Ikoyi. With panoramic views of Lagos, a private terrace, gym access, and concierge services, this property is perfect for those seeking the finest accommodation. Features 5 bedrooms and a home theater.',
                'address' => '1A Osborne Road, Ikoyi',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'price_per_night' => 350000.00,
                'bedrooms' => 5,
                'bathrooms' => 6,
                'max_guests' => 10,
                'status' => 'active',
            ],
        ];

        // Get all amenity IDs for random assignment
        $amenityIds = Amenity::pluck('id')->toArray();

        foreach ($properties as $propertyData) {
            $property = Property::create(array_merge(
                ['user_id' => $owner->id],
                $propertyData
            ));

            // Randomly assign 3-8 amenities to each property
            if (!empty($amenityIds)) {
                $randomAmenities = array_rand(array_flip($amenityIds), rand(3, min(8, count($amenityIds))));
                $property->amenities()->attach($randomAmenities);
            }
        }
    }
}
